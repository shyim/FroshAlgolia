<?php
namespace SwAlgolia\Services;

use AlgoliaSearch\Client;
use Doctrine\ORM\AbstractQuery;
use Shopware\Bundle\StoreFrontBundle\Service\Core\ContextService;
use Shopware\Bundle\StoreFrontBundle\Service\Core\ProductService;
use Shopware\Bundle\StoreFrontBundle\Struct\Media;
use Shopware\Bundle\StoreFrontBundle\Struct\Product;
use Shopware\Components;

/**
 * Class AlgoliaIndexService
 * @package SwAlgolia\Services
 */
class AlgoliaIndexService
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
     * AlgoliaIndexService constructor.
     * @param Components\Logger $logger
     */
    public function __construct(Components\Logger $logger, ContextService $context, ProductService $productService) {

        $this->logger = $logger;
        $this->context = $context;
        $this->productService = $productService;

        // Construct the context
        $repository = Shopware()->Container()->get('models')->getRepository('Shopware\Models\Shop\Shop');
        $shop = $repository->getActiveDefault();
        $shop->registerResources();
        //$this->context = Components\Routing\Context::createFromShop($shop, Shopware()->Container()->get('config'));
    }

    public function push() {

        // Grab the plugin config
        $pluginConfig = Shopware()->Container()->get('shopware.plugin.cached_config_reader')->getByPluginName('SwAlgolia');

        // Init the API client
        $client = new Client($pluginConfig['algolia-application-id'],$pluginConfig['algolia-admin-api-key']);
        $index = $client->initIndex($pluginConfig['index-name']);

        $articles = Shopware()->Db()->fetchCol('SELECT ordernumber FROM s_articles_details WHERE kind = 1');

        $router = Shopware()->Container()->get('router');
        $data = [];

        foreach($articles as $article) {
            // Get product object
            /** @var Product $product */
            $product = $this->productService->get($article, $this->context->getShopContext());

            if (!$product) {
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

            $category = $product->getCategories();
            $category = end($category);

            $data[] = array(
                'objectID' => $product->getNumber(),
                'name' => $product->getName(),
                'number' => $product->getNumber(),
                'manufacturer_name' => $product->getManufacturer()->getName(),
                'price' => round($product->getCheapestPrice()->getCalculatedPrice(), 2),
                'link' => $link,
                'description' => strip_tags($product->getShortDescription()),
                'ean' => $product->getEan(),
                'image' => $image,
                'category' => $category->getName()
            );
            $this->logger->addInfo('Successfully exported article {number} - {articleName}', array('number' => $product->getNumber(), 'articleName' => $product->getName()));
        }

        $index->addObjects($data);

    }

}
