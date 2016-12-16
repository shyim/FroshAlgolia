<?php

namespace SwAlgolia\Services;

use Shopware\Bundle\StoreFrontBundle\Struct\Shop;

interface SettingsInterface
{
    /**
     * @param Shop $shop
     * @return array|null
     */
    public function get(Shop $shop);
}
