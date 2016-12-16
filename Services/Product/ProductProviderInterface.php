<?php

namespace SwAlgolia\Services\Product;

use SwAlgolia\Structs\Product;
use Shopware\Bundle\StoreFrontBundle\Struct\Shop;

interface ProductProviderInterface
{
    /**
     * @param Shop $shop
     * @param string[] $numbers
     * @return Product[]
     */
    public function get(Shop $shop, $numbers);
}
