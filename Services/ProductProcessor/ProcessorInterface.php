<?php

namespace SwAlgolia\Services\ProductProcessor;

use Shopware\Bundle\StoreFrontBundle\Struct\Product;
use SwAlgolia\Structs\Article;

/**
 * Interface ProcessorInterface
 */
interface ProcessorInterface
{
    /**
     * @param Product $product Shopware Product
     * @param Article $article Algolia Product
     * @return void
     */
    public function process(Product $product, Article $article);
}