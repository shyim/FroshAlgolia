<?php

namespace SwAlgolia\Services\Property;

use SwAlgolia\Structs\Backlog;
use SwAlgolia\Structs\ShopIndex;
use SwAlgolia\Subscriber\ORMBacklogSubscriber;
use SwAlgolia\SynchronizerInterface;

class PropertySynchronizer implements SynchronizerInterface
{
    /**
     * @var PropertyIndexer
     */
    private $propertyIndexer;

    /**
     * @param PropertyIndexer $propertyIndexer
     */
    public function __construct(PropertyIndexer $propertyIndexer)
    {
        $this->propertyIndexer = $propertyIndexer;
    }

    /**
     * {@inheritdoc}
     */
    public function synchronize(ShopIndex $shopIndex, $backlog)
    {
        $ids = $this->getPropertyIdsOfBacklog($backlog);

        if (empty($ids)) {
            return;
        }

        $size = 100;
        $chunks = array_chunk($ids, $size);
        foreach ($chunks as $chunk) {
            $this->propertyIndexer->indexProperties($shopIndex, $chunk);
        }
    }

    /**
     * @param Backlog[] $backlogs
     * @return int[]
     */
    private function getPropertyIdsOfBacklog($backlogs)
    {
        $ids = [];
        foreach ($backlogs as $backlog) {
            $payload = $backlog->getPayload();

            switch ($backlog->getEvent()) {
                case ORMBacklogSubscriber::EVENT_PROPERTY_GROUP_DELETED:
                case ORMBacklogSubscriber::EVENT_PROPERTY_GROUP_INSERTED:
                case ORMBacklogSubscriber::EVENT_PROPERTY_GROUP_UPDATED:
                    $ids[] = $payload['id'];
                    break;

                case ORMBacklogSubscriber::EVENT_PROPERTY_OPTION_DELETED:
                case ORMBacklogSubscriber::EVENT_PROPERTY_OPTION_INSERTED:
                case ORMBacklogSubscriber::EVENT_PROPERTY_OPTION_UPDATED:
                    $ids[] = $payload['groupId'];
                    break;
            }
        }
        return array_unique(array_filter($ids));
    }
}
