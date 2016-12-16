<?php

namespace SwAlgolia\Services;

use Shopware\Bundle\StoreFrontBundle\Struct\Shop;

interface MappingInterface
{
    /**
     * @return string
     */
    public function getType();

    /**
     * @param Shop $shop
     * @return array
     */
    public function get(Shop $shop);
}
