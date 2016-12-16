<?php

namespace SwAlgolia\Services\Property;

use Shopware\Bundle\StoreFrontBundle\Struct\Property\Group;
use Shopware\Bundle\StoreFrontBundle\Struct\Shop;

interface PropertyProviderInterface
{
    /**
     * @param Shop $shop
     * @param int[] $groupIds
     * @return Group[]
     */
    public function get(Shop $shop, $groupIds);
}
