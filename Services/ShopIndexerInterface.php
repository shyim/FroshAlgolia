<?php

namespace SwAlgolia\Services;

use SwAlgolia\Console\ProgressHelperInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\Shop;

/**
 * Interface ShopIndexerInterface
 * @package SwAlgolia\Services
 */
interface ShopIndexerInterface
{
    /**
     * @param Shop $shop
     * @param ProgressHelperInterface $helper
     */
    public function index(Shop $shop, ProgressHelperInterface $helper);

    /**
     * Remove unused indices
     *
     * @return void
     */
    public function cleanupIndices();
}
