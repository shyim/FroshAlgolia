<?php

namespace SwAlgolia\Services;

use SwAlgolia\Structs\Backlog;
use SwAlgolia\Structs\ShopIndex;

/**
 * Interface BacklogProcessorInterface.
 */
interface BacklogProcessorInterface
{
    /**
     * @param Backlog[] $backlogs
     */
    public function add($backlogs);

    /**
     * @param ShopIndex $shopIndex
     * @param Backlog[] $backlogs
     */
    public function process(ShopIndex $shopIndex, $backlogs);
}
