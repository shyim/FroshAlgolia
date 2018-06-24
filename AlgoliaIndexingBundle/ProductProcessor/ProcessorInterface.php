<?php declare(strict_types=1);

namespace FroshAlgolia\AlgoliaIndexingBundle\ProductProcessor;

use FroshAlgolia\AlgoliaIndexingBundle\Struct\AlgoliaProduct;
use Shopware\Bundle\StoreFrontBundle\Struct\Product;

/**
 * Interface ProcessorInterface.
 */
interface ProcessorInterface
{
    /**
     * @param Product $product Shopware Product
     * @param AlgoliaProduct $algoliaProduct
     * @param array $shopConfig Shop Configuration
     * @return void
     */
    public function process(Product $product, AlgoliaProduct $algoliaProduct, array $shopConfig) : void;
}
