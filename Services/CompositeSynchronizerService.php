<?php

namespace SwAlgolia\Services;

use SwAlgolia\Structs\ShopIndex;

/**
 * Class CompositeSynchronizerService
 * @package SwAlgolia\Services
 */
class CompositeSynchronizerService implements SynchronizerInterface
{
    /**
     * @var SynchronizerInterface[]
     */
    private $synchronizer;

    /**
     * @param SynchronizerInterface[] $synchronizer
     */
    public function __construct($synchronizer)
    {
        $this->synchronizer = $synchronizer;
    }

    /**
     * {@inheritdoc}
     */
    public function synchronize(ShopIndex $shopIndex, $backlogs)
    {
        foreach ($this->synchronizer as $synchronizer) {
            $synchronizer->synchronize($shopIndex, $backlogs);
        }
    }
}
