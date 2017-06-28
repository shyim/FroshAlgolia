<?php

namespace SwAlgolia\Services\ProductProcessor;

use Shopware\Bundle\StoreFrontBundle\Struct\Product;
use SwAlgolia\Structs\Article;

/**
 * Interface ProcessorInterface.
 */
interface ProcessorInterface
{
    /**
     * @param Product $product    Shopware Product
     * @param Article $article    Algolia Product
     * @param array   $shopConfig Shop Configuration
     */
    public function process(Product $product, Article $article, array $shopConfig);
}
