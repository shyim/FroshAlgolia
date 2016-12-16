<?php

namespace SwAlgolia\Services;

use SwAlgolia\Console\ProgressHelperInterface;
use SwAlgolia\Structs\ShopIndex;

/**
 * Interface DataIndexerInterface
 * @package SwAlgolia\Services
 */
interface DataIndexerInterface
{
    /**
     * @param ShopIndex $index
     * @param ProgressHelperInterface $progress
     */
    public function populate(ShopIndex $index, ProgressHelperInterface $progress);
}
