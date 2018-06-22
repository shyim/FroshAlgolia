<?php declare(strict_types=1);

namespace FroshAlgolia\AlgoliaIndexingBundle\ProductProcessor;

use FroshAlgolia\Structs\Article;
use Shopware\Bundle\StoreFrontBundle\Struct\Product;

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
