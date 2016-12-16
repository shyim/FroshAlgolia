<?php

namespace SwAlgolia\Services;

use SwAlgolia\Structs\Backlog;
use SwAlgolia\Structs\ShopIndex;

/**
 * Interface SynchronizerInterface
 * @package SwAlgolia\Services
 */
interface SynchronizerInterface
{
    /**
     * @param ShopIndex $shopIndex
     * @param Backlog[] $backlogs
     */
    public function synchronize(ShopIndex $shopIndex, $backlogs);
}
