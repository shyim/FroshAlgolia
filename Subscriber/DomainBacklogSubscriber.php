<?php

namespace SwAlgolia\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs as EventArgs;
use Shopware\Components\DependencyInjection\Container;
use SwAlgolia\Structs\Backlog;

/**
 * Class DomainBacklogSubscriber
 * @package SwAlgolia\Subscriber
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
            'product_stock_was_changed' => 'onProductStockWasChanged'
        ];
    }

    /**
     * @param EventArgs $eventArgs
     */
    public function onProductStockWasChanged(EventArgs $eventArgs)
    {
        $backlog = new Backlog(ORMBacklogSubscriber::EVENT_VARIANT_UPDATED, ['number' => $eventArgs->get('number')]);
        $this->container->get('sw_algolia.backlog_processor')->add([$backlog]);
    }

}
