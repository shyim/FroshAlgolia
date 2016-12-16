<?php

namespace SwAlgolia\Subscriber;

use Doctrine\Common\Collections\ArrayCollection;
use Enlight\Event\SubscriberInterface;
use SwAlgolia\Commands\AnalyzeCommand;
use SwAlgolia\Commands\BacklogClearCommand;
use SwAlgolia\Commands\BacklogSyncCommand;
use SwAlgolia\Commands\IndexCleanupCommand;
use SwAlgolia\Commands\IndexPopulateCommand;
use SwAlgolia\Commands\SwitchAliasCommand;

/**
 * Class ServiceSubscriber
 * @package SwAlgolia\Subscriber
 */
class ServiceSubscriber implements SubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Shopware_Console_Add_Command' => ['addCommands']
        ];
    }

    /**
     * @return ArrayCollection
     */
    public function addCommands()
    {
        return new ArrayCollection([
            new IndexPopulateCommand(),
            new IndexCleanupCommand(),
            new BacklogClearCommand(),
            new BacklogSyncCommand(),
            new SwitchAliasCommand(),
            new AnalyzeCommand(),
        ]);
    }
}
