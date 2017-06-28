<?php

namespace SwAlgolia\Services;

use Doctrine\DBAL\Connection;
use Enlight_Controller_Router;
use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Service\Core\ProductService;
use Shopware\Bundle\StoreFrontBundle\Struct\Product;
use Shopware\Models\Shop\Shop;
use SwAlgolia\Services\ProductProcessor\ProcessorInterface;
use SwAlgolia\Structs\Article;

class ProductIndexer
{
    /**
     * @var ContextServiceInterface
     */
    private $context;

    /**
     * @var ProductService
     */
    private $productService;

    /**
     * @var ProcessorInterface[]
     */
    private $processor = [];

    /**
     * @var Enlight_Controller_Router
     */
    private $router;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * ProductIndexer constructor.
     *
     * @param ContextServiceInterface   $contextService
     * @param ProductService            $productService
     * @param Enlight_Controller_Router $router
     * @param Connection                $connection
     * @param array                     $processor
     */
    public function __construct(
        ContextServiceInterface $contextService,
        ProductService $productService,
        Enlight_Controller_Router $router,
        Connection $connection,
        array $processor
    ) {
        $this->context = $contextService;
        $this->productService = $productService;
        $this->router = $router;
        $this->processor = $processor;
        $this->connection = $connection;
    }

    /**
     * @param Shop $shop
     * @param $chunkSize
     * @param $shopConfig
     *
     * @return array
     */
    public function index(Shop $shop, $chunkSize, array $shopConfig)
    {
        $context = $this->context->createShopContext($shop->getId());
        $data = [];
        $chunk = $this->getProducts($shop, $chunkSize);

        foreach ($chunk as $chunkKey => $products) {
            $products = $this->productService->getList($products, $context);

            /** @var Product $product */
            foreach ($products as $product) {
                $article = new Article();
                $assembleParams = [
                    'module' => 'frontend',
                    'sViewport' => 'detail',
                    'sArticle' => $product->getId(),
                ];
                $link = $this->router->assemble($assembleParams);

                $article->setCurrencySymbol($shop->getCurrency()->getSymbol());
                $article->setLink($link);

                foreach ($this->processor as $processor) {
                    $processor->process($product, $article, $shopConfig);
                }

                $data[$chunkKey][] = $article->toArray();
            }
        }

        return $data;
    }

    /**
     * @param Shop $shop
     * @param $chunkSize
     *
     * @return array
     */
    private function getProducts(Shop $shop, $chunkSize)
    {
        $products = $this->connection->executeQuery('SELECT DISTINCT s_articles_details.ordernumber FROM s_articles_details INNER JOIN s_articles_categories_ro ON(s_articles_categories_ro.articleID = s_articles_details.articleID AND s_articles_categories_ro.categoryID = ?) WHERE kind = 1 AND active = 1', [
            $shop->getCategory()->getId(),
        ])->fetchAll(\PDO::FETCH_COLUMN);

        return array_chunk($products, $chunkSize);
    }
}
