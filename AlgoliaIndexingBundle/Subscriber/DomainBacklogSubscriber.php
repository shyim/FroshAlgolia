<?php declare(strict_types=1);

namespace FroshAlgolia\AlgoliaIndexingBundle\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs as EventArgs;
use FroshAlgolia\AlgoliaIndexingBundle\Service\BacklogProcessorInterface;
use FroshAlgolia\AlgoliaIndexingBundle\Struct\Backlog;

/**
 * Class DomainBacklogSubscriber.
 */
class DomainBacklogSubscriber implements SubscriberInterface
{
    /**
     * @var BacklogProcessorInterface
     */
    private $backlogProcessor;

    /**
     * @param BacklogProcessorInterface $backlogProcessor
     */
    public function __construct(BacklogProcessorInterface $backlogProcessor)
    {
        $this->backlogProcessor = $backlogProcessor;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'product_stock_was_changed' => 'onProductStockWasChanged',
        ];
    }

    /**
     * @param EventArgs $eventArgs
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function onProductStockWasChanged(EventArgs $eventArgs)
    {
        $backlog = new Backlog(ORMBacklogSubscriber::EVENT_VARIANT_UPDATED, ['number' => $eventArgs->get('number')]);
        $this->backlogProcessor->add([$backlog]);
    }
}
