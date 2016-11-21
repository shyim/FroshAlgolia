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
 * Class SyncService
 * @package SwAlgolia\Services
 */
class SyncService
{
    /**
     * @var Components\Logger
     */
    private $logger = null;

    /**
     * @var Core\ContextService
     */
    private $context = null;

    /**
     * @var ProductService
     */
    private $productService = null;

    /**
     * @var AlgoliaService
     */
    private $algoliaService = null;

    /**
     * @var SyncHelperService
     */
    private $syncHelperService = null;

    /**
     * @var EntityManager
     */
    private $em = null;

    /**
     * @var array
     */
    private $pluginConfig = null;

    /**
     * SyncService constructor.
     * @param Components\Logger $logger
     * @param Core\ContextService $context
     * @param ProductService $productService
     * @param AlgoliaService $algoliaService
     * @param SyncHelperService $syncHelperService
     */
    public function __construct(Components\Logger $logger, Core\ContextService $context, ProductService $productService, AlgoliaService $algoliaService, SyncHelperService $syncHelperService)
    {

        $this->logger = $logger;
        $this->context = $context;
        $this->productService = $productService;
        $this->algoliaService = $algoliaService;
        $this->syncHelperService = $syncHelperService;
        $this->em = Shopware()->Container()->get('models');

        // Grab the plugin config
        $this->pluginConfig = Shopware()->Container()->get('shopware.plugin.cached_config_reader')->getByPluginName('SwAlgolia');

    }

    /**
     * Syncs complete article data to Angolia
     * @return bool
     * @throws \Exception
     */
    public function fullSync()
    {

        // Get all shops
        if (!$shops = $this->getShops()):
            throw new \Exception('No active shop found.');
        endif;

        // Iterate over all shops
        foreach ($shops as $shop):

            // Construct the context
            $shop->registerResources();

            // Clear the Algolia index for this shop
            $this->deleteIndex($shop);

            // Create the Algolia index for this shop
            $this->createIndices($shop);

            // Limit articles if required
            $limit = '';
            if($this->pluginConfig['limit-indexed-products-for-test'] > 0):
                $limit = ' LIMIT 0,'.$this->pluginConfig['limit-indexed-products-for-test'];
            endif;

            // Get all articles
            $articles = Shopware()->Db()->fetchCol('SELECT ordernumber FROM s_articles_details WHERE kind = 1 and active = 1'.$limit);

            $router = Shopware()->Container()->get('router');
            $data = [];
            $i = 0;

            // Iterate over all found articles
            foreach ($articles as $article):

                $i++;

                // Get product object
                /** @var Product $product */
                if ($product = $this->productService->get($article, $this->context->getShopContext())) {

                    // Get the SEO URL
                    $assembleParams = array(
                        'module' => 'frontend',
                        'sViewport' => 'detail',
                        'sArticle' => $product->getId()
                    );
                    $link = $router->assemble($assembleParams);

                    $media = $product->getMedia();
                    $image = null;

                    if (!empty($media)) {
                        /** @var Media $mediaObject */
                        $mediaObject = current($media);
                        $image = $mediaObject->getThumbnail(0)->getSource();
                    }

                    $articleStruct = new ArticleStruct();
                    $articleStruct->setObjectID($product->getNumber());
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
                    $articleStruct->setVotes($product->getVoteAverage());
                    $data[] = $articleStruct->toArray();

                } else {
                    $this->logger->addWarning('Could not generate product struct for article {number} - {articleName} for export. Product not exported.', array('number' => $article));
                }

                // Push data to Algolia if sync-batch size is reached
                if (count($data) % $this->pluginConfig['sync-batch-size'] == 0 || $i == count($articles)):

                    // Push data to Algolia
                    $this->algoliaService->push($shop, $data, $this->syncHelperService->buildIndexName($shop));
                    $data = [];

                endif;

            endforeach;

        endforeach;

        return true;

    }

    /**
     * This method consumes all events where entity data (e.g. articles) is changed and submits
     * the changed data on the fly to Algolia.
     * @param Struct $product
     */
    public function liveSync(Struct $product)
    {

        // @TODO TBD

    }

    /**
     * Creates and inits all indices and replica indices for a given shop
     * @param $shop
     */
    private function createIndices($shop)
    {

        // Create main index
        $indexName = $this->syncHelperService->buildIndexName($shop);
        $index = $this->algoliaService->initIndex($indexName);
        $attributesForFaceting = explode(',', $this->pluginConfig['index-faceting-attributes']);

        // Create indices, replica indices and define settings
        $indexSettings = array(
            'attributesToIndex' => explode(',',$this->pluginConfig['index-searchable-attributes']),
            'customRanking' => explode(',', $this->pluginConfig['index-custom-ranking-attributes']),
            'attributesForFaceting'  => $attributesForFaceting,
            'replicas' => $this->getReplicaNames($indexName)
        );
        $settingsResponse = $this->algoliaService->pushIndexSettings($indexSettings, $index);

        // Wait for the task to be completed (to make sure replica indices are ready)
        $index->waitTask($settingsResponse['taskID']);

        // Define replica settings
        $replicaIndices = explode('|',$this->pluginConfig['index-replicas-custom-ranking-attributes']);
        foreach($replicaIndices as $replicaIndex):

            $replicaIndexSettings = explode(',',$replicaIndex);

            // Build the key / name for the replica index
            $nameElements = explode('(',$replicaIndexSettings[0]);
            $replicaIndexName = $indexName .'_'. rtrim($nameElements[1],')') . '_' . $nameElements[0];

            $this->algoliaService->pushIndexSettings(array(
                'ranking' => $replicaIndexSettings,
                'attributesForFaceting'  => $attributesForFaceting
            ), null, $replicaIndexName);

        endforeach;

    }

    /**
     * Gets an array of all replica indices that needs to be created for a main index
     * @param $indexName
     * @return array
     */
    private function getReplicaNames($indexName) {

        $names = [];

        // Get the replicas from config
        $replicaIndices = explode('|',$this->pluginConfig['index-replicas-custom-ranking-attributes']);

        foreach($replicaIndices as $replicaIndex):

            $replicaIndexElements = explode(',',$replicaIndex);

            // Build the key / name for the replica index
            $nameElements = explode('(',$replicaIndexElements[0]);
            $replicaIndexName = $indexName .'_'. rtrim($nameElements[1],')') . '_' . $nameElements[0];

            $names[] = $replicaIndexName;

        endforeach;

        return $names;

    }

    /**
     * Deletes all indices for a shop
     * @param Shop $shop
     * @return void
     */
    private function deleteIndex(Shop $shop) {

        $indexName = $this->syncHelperService->buildIndexName($shop);

        // Delete main index
        $this->algoliaService->deleteIndex($indexName);

    }

    /**
     * Get all product attributes
     * @param Product $product
     * @return array
     */
    private function getAttributes(Product $product)
    {

        $data = [];

        if (!isset($product->getAttributes()['core'])) {
            return [];
        }

        $attributes = $product->getAttributes()['core']->toArray();

        $blockedAttributes = explode(',', $this->pluginConfig['blocked-article-attributes']);

        foreach ($attributes as $key => $value):

            // Skip this attribute if it´s on the list of blocked attributes
            if (false != array_search($key, $blockedAttributes, true)) continue;

            // Skip this attribute if its value is null or ''
            if (!$value || $value == '') continue;

            // Map value to data array
            $data[$key] = $value;

        endforeach;

        return $data;

    }

    /**
     * Prepare categories for data article
     * @param Product $product
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
     * Fetches all product properties as an array
     * @param Product $product
     * @return array
     */
    private function getProperties(Product $product)
    {

        $properties = [];

        if ($set = $product->getPropertySet()):

            $groups = $set->getGroups();

            foreach ($groups as $group):
                $options = $group->getOptions();

                foreach ($options as $option):
                    $properties[$group->getName()] = $option->getName();
                endforeach;

            endforeach;

        endif;

        return $properties;


    }

    /**
     * @return array
     */
    private function getShops()
    {

        return $this->em->getRepository(Shop::class)->findBy(array('active' => true));

    }



}