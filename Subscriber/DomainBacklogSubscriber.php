<?php

namespace FroshAlgolia\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs as EventArgs;
use Shopware\Components\DependencyInjection\Container;
use FroshAlgolia\Structs\Backlog;

/**
 * Class DomainBacklogSubscriber.
 */
class DomainBacklogSubscriber implements SubscriberInterface
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
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
     */
    public function onProductStockWasChanged(EventArgs $eventArgs)
    {
        $backlog = new Backlog(ORMBacklogSubscriber::EVENT_VARIANT_UPDATED, ['number' => $eventArgs->get('number')]);
        $this->container->get('frosh_algolia.backlog_processor')->add([$backlog]);
    }
}
