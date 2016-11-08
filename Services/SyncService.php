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

/**
 * Class SyncService
 * @package SwAlgolia\Services
 */
class SyncService
{

    /**
     * @var null|Components\Logger
     */
    private $logger = null;

    /**
     * @var null|ContextService
     */
    private $context = null;

    /**
     * @var null|ProductService
     */
    private $productService = null;

    /**
     * @var null|AlgoliaService
     */
    private $algoliaService = null;

    /**
     * @var null|EntityManager
     */
    private $em = null;

    /**
     * @var null|array
     */
    private $pluginConfig = null;

    /**
     * SyncService constructor.
     * SyncService constructor.
     * @param Components\Logger $logger
     * @param Core\ContextService $context
     * @param ProductService $productService
     * @param AlgoliaService $algoliaService
     */
    public function __construct(Components\Logger $logger, Core\ContextService $context, ProductService $productService, AlgoliaService $algoliaService) {

        $this->logger = $logger;
        $this->context = $context;
        $this->productService = $productService;
        $this->algoliaService = $algoliaService;
        $this->em = Shopware()->Container()->get('models');

        // Grab the plugin config
        $this->pluginConfig = Shopware()->Container()->get('shopware.plugin.cached_config_reader')->getByPluginName('SwAlgolia');

    }

    /**
     * Syncs complete article data to Angolia
     * @return bool
     * @throws \Exception
     */
    public function fullSync() {

        // Grab the plugin config
        $pluginConfig = Shopware()->Container()->get('shopware.plugin.cached_config_reader')->getByPluginName('SwAlgolia');

        // Get all shops
        if (!$shops = $this->getShops()):
            throw new \Exception('No active shop found.');
        endif;

        // Iterate over all shops
        foreach($shops as $shop):

            // Construct the context
            $shop->registerResources();

            // Get all articles
            $articles = Shopware()->Db()->fetchCol('SELECT ordernumber FROM s_articles_details WHERE kind = 1 and active = 1');

            $router = Shopware()->Container()->get('router');
            $data = [];

            $i=1;

            // Iterate over all found articles
            foreach($articles as $article):


                // Get product object
                /** @var Product $product */
                if (!$product = $this->productService->get($article, $this->context->getShopContext())) {
                    $this->logger->addAlert('Could not generate product struct for article {number} - {articleName} for export. Product not exported.', array('number' => $article));
                    continue;
                }

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
                $articleStruct->setPrice(round($product->getCheapestPrice()->getCalculatedPrice(), 2));
                $articleStruct->setLink($link);
                $articleStruct->setDescription(strip_tags($product->getShortDescription()));
                $articleStruct->setEan($product->getEan());
                $articleStruct->setImage($image);
                $articleStruct->setCategories($this->getCategories($product)['categoryNames']);
                $articleStruct->setCategoryIds($this->getCategories($product)['categoryIds']);
                $articleStruct->setAttributes($this->getAttributes($product));
                $data[] = $articleStruct->toArray();

                // Push data to Algolia if sync-batch size is reached
                if($i % $this->pluginConfig['sync-batch-size'] == 0 || $i == count($articles)):
                    // Push data to Algolia
                    $this->algoliaService->push($shop, $data, $pluginConfig['index-prefix-name'] . '-' . $shop->getId());
                    $data = null;
                endif;

                // @TODO remove test limitation
                if($i>=20) break;
                $i++;

            endforeach;

        endforeach;

    }

    /**
     * Get all product attributes
     * @param Product $product
     * @return array
     */
    private function getAttributes(Product $product) {

        $data = [];
        $attributes = $product->getAttributes()['core']->toArray();

        $blockedAttributes = explode(',',$this->pluginConfig['blocked-article-attributes']);

        foreach($attributes as $key => $value):

            // Skip this attribute if it´s on the list of blocked attributes
            if (false != array_search($key,$blockedAttributes,true)) continue;

            // Skip this attribute if its value is null or ''
            if(!$value || $value=='') continue;

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
    private function getCategories(Product $product) {

        $categories = $product->getCategories();
        $data = [];

        // Remove main category (German)
        if (isset($categories[0])) {
            unset($categories[0]);
        }

        foreach ($categories as $category) {
            $data['categoryNames'][] = $category->getName();
            $data['categoryIds'][] = $category->getId();
        }

        return $data;

    }

    /**
     * @return array
     */
    private function getShops() {

        return $this->em->getRepository('Shopware\Models\Shop\Shop')->findBy(array('active' => true));

    }

}
