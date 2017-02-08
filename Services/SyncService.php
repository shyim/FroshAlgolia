<?php

namespace SwAlgolia\Services;

use Doctrine\ORM\EntityManager;
use Shopware\Bundle\StoreFrontBundle\Service\Core;
use Shopware\Bundle\StoreFrontBundle\Service\Core\ProductService;
use Shopware\Bundle\StoreFrontBundle\Struct\Media;
use Shopware\Bundle\StoreFrontBundle\Struct\Product;
use Shopware\Components;
use Shopware\Models\Shop\Shop;
use SwAlgolia\Structs\Article as ArticleStruct;
use SwAlgolia\Structs\Struct;

/**
 * Class SyncService.
 *
 * Todo: CLEANUPPPP
 */
class SyncService
{
    /**
     * @var Components\Logger
     */
    private $logger;

    /**
     * @var Core\ContextService
     */
    private $context;

    /**
     * @var ProductService
     */
    private $productService;

    /**
     * @var AlgoliaService
     */
    private $algoliaService;

    /**
     * @var SyncHelperService
     */
    private $syncHelperService;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var ConfigReader
     */
    private $configReader;

    /**
     * @var array
     */
    private $pluginConfig;

    /**
     * @var array
     */
    private $shopConfig = [];

    /**
     * SyncService constructor.
     *
     * @param Components\Logger   $logger
     * @param Core\ContextService $context
     * @param ProductService      $productService
     * @param AlgoliaService      $algoliaService
     * @param SyncHelperService   $syncHelperService
     */
    public function __construct(Components\Logger $logger, Core\ContextService $context, ProductService $productService, AlgoliaService $algoliaService, SyncHelperService $syncHelperService)
    {
        $this->logger = $logger;
        $this->context = $context;
        $this->productService = $productService;
        $this->algoliaService = $algoliaService;
        $this->syncHelperService = $syncHelperService;
        $this->em = Shopware()->Container()->get('models');


        $this->configReader = Shopware()->Container()->get('sw_algolia.config_reader');
        // Grab the plugin config
        $this->pluginConfig = Shopware()->Container()->get('shopware.plugin.cached_config_reader')->getByPluginName('SwAlgolia');
    }

    /**
     * Syncs complete article data to Angolia.
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function fullSync()
    {
        // Get all shops
        if (!$shops = $this->getShops()) {
            throw new \Exception('No active shop found.');
        }

        // Iterate over all shops
        /** @var Shop $shop */
        foreach ($shops as $shop) {
            $this->shopConfig = $this->configReader->read($shop);

            // Construct the context
            $shop->registerResources();

            // Clear the Algolia index for this shop
            $this->deleteIndex($shop);

            // Create the Algolia index for this shop
            $this->createIndices($shop);

            // Limit articles if required
            $limit = '';

            if ($this->pluginConfig['limit-indexed-products-for-test'] > 0) {
                $limit = ' LIMIT 0,'.$this->pluginConfig['limit-indexed-products-for-test'];
            }

            // Get all articles
            $articles = Shopware()->Db()->fetchCol('SELECT s_articles_details.ordernumber FROM s_articles_details INNER JOIN s_articles_categories_ro ON(s_articles_categories_ro.articleID = s_articles_details.articleID AND s_articles_categories_ro.categoryID = ?) WHERE kind = 1 and active = 1'.$limit, [
                $shop->getCategory()->getId(),
            ]);

            $router = Shopware()->Container()->get('router');
            $data = [];
            $i = 0;

            // Iterate over all found articles
            foreach ($articles as $article) {
                ++$i;

                // Get product object
                /** @var Product $product */
                if ($product = $this->productService->get($article, $this->context->getShopContext())) {
                    // Get the SEO URL
                    // @TODO Fix wrong link when the shop uses a virtual path (e.g. /de or /en)
                    $assembleParams = [
                        'module' => 'frontend',
                        'sViewport' => 'detail',
                        'sArticle' => $product->getId(),
                    ];
                    $link = $router->assemble($assembleParams);

                    // Get the media
                    $media = $product->getMedia();
                    $image = null;

                    if (!empty($media)) {
                        /** @var Media $mediaObject */
                        $mediaObject = current($media);
                        $image = $mediaObject->getThumbnail(0)->getSource();
                    }

                    // Get the votes
                    $voteAvgPoints = 0;
                    $votes = $product->getVoteAverage();
                    if ($votes) {
                        $voteAvgPoints = (int) $votes->getPointCount()[0]['points'];
                    }

                    // Buid the article struct
                    $articleStruct = new ArticleStruct();
                    $articleStruct->setObjectID($product->getNumber());
                    $articleStruct->setArticleId($product->getId());
                    $articleStruct->setName($product->getName());
                    $articleStruct->setNumber($product->getNumber());
                    $articleStruct->setManufacturerName($product->getManufacturer()->getName());
                    $articleStruct->setCurrencySymbol($shop->getCurrency()->getSymbol());
                    $articleStruct->setPrice(round($product->getCheapestPrice()->getCalculatedPrice(), 2));
                    $articleStruct->setLink($link);
                    $articleStruct->setDescription(strip_tags($product->getShortDescription()));
                    $articleStruct->setEan($product->getEan());
                    $articleStruct->setImage($image);
                    $articleStruct->setCategories($this->getCategories($product)['categoryNames']);
                    $articleStruct->setAttributes($this->getAttributes($product));
                    $articleStruct->setProperties($this->getProperties($product));
                    $articleStruct->setSales($product->getSales());
                    $articleStruct->setVotes($votes);
                    $articleStruct->setVoteAvgPoints($voteAvgPoints);
                    $data[] = $articleStruct->toArray();
                } else {
                    $this->logger->addWarning('Could not generate product struct for article {number} for export. Product not exported.', ['number' => $article]);
                }

                // Push data to Algolia if sync-batch size is reached
                if (count($data) % $this->pluginConfig['sync-batch-size'] == 0 || $i === count($articles)) {
                    // Push data to Algolia
                    $this->algoliaService->push($shop, $data, $this->syncHelperService->buildIndexName($shop));
                    $data = [];
                }
            }
        }

        return true;
    }

    /**
     * This method consumes all events where entity data (e.g. articles) is changed and submits
     * the changed data on the fly to Algolia.
     *
     * @param Struct $product
     */
    public function liveSync(Struct $product)
    {
        // @TODO TBD
    }

    /**
     * Creates and inits all indices and replica indices for a given shop.
     *
     * @param $shop
     */
    private function createIndices($shop)
    {
        // Create main index
        $indexName = $this->syncHelperService->buildIndexName($shop);
        $index = $this->algoliaService->initIndex($indexName);

        $attributesForFaceting = array_column($this->shopConfig['facetAttributes'], 'name');

        // Create indices, replica indices and define settings
        $indexSettings = [
            'attributesToIndex' => array_column($this->shopConfig['searchAttributes'], 'name'),
            'customRanking' => $this->configReader->convertToAlgoliaRanking($this->shopConfig['rankingIndexAttributes']),
            'attributesForFaceting' => $attributesForFaceting,
            'replicas' => $this->getReplicaNames($indexName),
        ];
        $settingsResponse = $this->algoliaService->pushIndexSettings($indexSettings, $index);

        // Wait for the task to be completed (to make sure replica indices are ready)
        $index->waitTask($settingsResponse['taskID']);

        // Define replica settings
        $replicaIndices = explode('|', $this->pluginConfig['index-replicas-custom-ranking-attributes']);

        foreach ($replicaIndices as $replicaIndex) {
            $replicaIndexSettings = explode(',', $replicaIndex);

            // Build the key / name for the replica index
            $nameElements = explode('(', $replicaIndexSettings[0]);
            $replicaIndexName = $indexName.'_'.rtrim($nameElements[1], ')').'_'.$nameElements[0];

            $params = [
                'ranking' => $replicaIndexSettings,
                'attributesForFaceting' => $attributesForFaceting,
            ];

            $this->algoliaService->pushIndexSettings($params, null, $replicaIndexName);
        }
    }

    /**
     * Gets an array of all replica indices that needs to be created for a main index.
     *
     * @param $indexName
     *
     * @return array
     */
    private function getReplicaNames($indexName)
    {
        $names = [];

        // Get the replicas from config
        $replicaIndices = explode('|', $this->pluginConfig['index-replicas-custom-ranking-attributes']);

        foreach ($replicaIndices as $replicaIndex) {
            $replicaIndexElements = explode(',', $replicaIndex);

            // Build the key / name for the replica index
            $nameElements = explode('(', $replicaIndexElements[0]);
            $replicaIndexName = $indexName.'_'.rtrim($nameElements[1], ')').'_'.$nameElements[0];

            $names[] = $replicaIndexName;
        }

        return $names;
    }

    /**
     * Deletes all indices for a shop.
     *
     * @param Shop $shop
     */
    private function deleteIndex(Shop $shop)
    {
        $indexName = $this->syncHelperService->buildIndexName($shop);

        // Delete main index
        $this->algoliaService->deleteIndex($indexName);
    }

    /**
     * Get all product attributes.
     *
     * @param Product $product
     *
     * @return array
     */
    private function getAttributes(Product $product)
    {
        $data = [];

        if (!isset($product->getAttributes()['core'])) {
            return [];
        }

        $attributes = $product->getAttributes()['core']->toArray();

        $blockedAttributes = array_column($this->shopConfig['blockedAttributes'], 'name');

        foreach ($attributes as $key => $value) {
            // Skip this attribute if it´s on the list of blocked attributes
            if (false != array_search($key, $blockedAttributes, true)) {
                continue;
            }

            // Skip this attribute if its value is null or ''
            if (!$value || $value == '') {
                continue;
            }

            // Map value to data array
            $data[$key] = $value;
        }

        return $data;
    }

    /**
     * Prepare categories for data article.
     *
     * @param Product $product
     *
     * @return array
     */
    private function getCategories(Product $product)
    {
        $categories = $product->getCategories();
        $data = [];

        // Remove main category (German)
        if (isset($categories[0])) {
            unset($categories[0]);
        }

        foreach ($categories as $category) {
            $data['categoryNames'][] = $category->getName();
        }

        return $data;
    }

    /**
     * Fetches all product properties as an array.
     *
     * @param Product $product
     *
     * @return array
     */
    private function getProperties(Product $product)
    {
        $properties = [];

        if ($set = $product->getPropertySet()) {
            $groups = $set->getGroups();

            foreach ($groups as $group) {
                $options = $group->getOptions();

                foreach ($options as $option) {
                    $properties[$group->getName()] = $option->getName();
                }
            }
        }

        return $properties;
    }

    /**
     * @return array
     */
    private function getShops()
    {
        return $this->em->getRepository(Shop::class)->findBy(['active' => true]);
    }
}
