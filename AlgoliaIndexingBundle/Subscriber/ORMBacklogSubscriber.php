<?php declare(strict_types=1);

namespace FroshAlgolia\AlgoliaIndexingBundle\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use FroshAlgolia\AlgoliaIndexingBundle\Service\BacklogProcessorInterface;
use FroshAlgolia\AlgoliaIndexingBundle\Struct\Backlog;
use Shopware\Components\ContainerAwareEventManager;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Article\Article as ArticleModel;
use Shopware\Models\Article\Detail as VariantModel;
use Shopware\Models\Article\Price as PriceModel;
use Shopware\Models\Article\Vote as VoteModel;
use Shopware\Models\Property\Option as PropertyGroupModel;
use Shopware\Models\Property\Value as PropertyOptionModel;
use Shopware\Models\Tax\Tax as TaxModel;

/**
 * Class ORMBacklogSubscriber.
 */
class ORMBacklogSubscriber implements EventSubscriber
{
    public const EVENT_VARIANT_DELETED = 'variant_deleted';
    public const EVENT_VARIANT_INSERTED = 'variant_inserted';
    public const EVENT_VARIANT_UPDATED = 'variant_updated';
    public const EVENT_PRICE_DELETED = 'variant_price_deleted';
    public const EVENT_PRICE_INSERTED = 'variant_price_inserted';
    public const EVENT_PRICE_UPDATED = 'variant_price_updated';
    public const EVENT_VOTE_DELETED = 'vote_deleted';
    public const EVENT_VOTE_INSERTED = 'vote_inserted';
    public const EVENT_VOTE_UPDATED = 'vote_updated';
    public const EVENT_TAX_UPDATED = 'tax_updated';
    public const EVENT_PROPERTY_GROUP_UPDATED = 'property_group_updated';
    public const EVENT_PROPERTY_OPTION_UPDATED = 'property_option_updated';

    /**
     * @var Backlog[]
     */
    private $queue = [];

    /**
     * @var array
     */
    private $inserts = [];

    /**
     * @var bool
     */
    private $eventRegistered = false;

    /**
     * @var ContainerAwareEventManager
     */
    private $eventsManager;

    /**
     * @var BacklogProcessorInterface
     */
    private $backlogProcessor;

    /**
     * @Todo: Fix cirucal reference error for injecting
     * ORMBacklogSubscriber constructor.
     */
    public function loadServices()
    {
        if ($this->eventsManager === null) {
            $this->eventsManager = Shopware()->Container()->get('events');
            $this->backlogProcessor = Shopware()->Container()->get('algolia_indexing_bundle.service_core.backlog_processor');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [Events::onFlush, Events::postFlush];
    }

    /**
     * @param OnFlushEventArgs $eventArgs
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $this->loadServices();

        /** @var $em ModelManager */
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        // Entity deletions
        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            $backlog = $this->getDeleteBacklog($entity);
            if (!$backlog) {
                continue;
            }
            $this->queue[] = $backlog;
        }

        // Entity Insertions
        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            $this->inserts[] = $entity;
        }

        // Entity updates
        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            $backlog = $this->getUpdateBacklog($entity);
            if (!$backlog) {
                continue;
            }
            $this->queue[] = $backlog;
        }
    }

    /**
     * @param PostFlushEventArgs $eventArgs
     */
    public function postFlush(PostFlushEventArgs $eventArgs)
    {
        $this->loadServices();

        foreach ($this->inserts as $entity) {
            $backlog = $this->getInsertBacklog($entity);
            if (!$backlog) {
                continue;
            }
            $this->queue[] = $backlog;
        }
        $this->inserts = [];

        $this->registerShutdownListener();
    }

    private function registerShutdownListener()
    {
        if ($this->eventRegistered) {
            return;
        }

        $this->eventRegistered = true;
        $this->eventsManager->addListener(
            'Enlight_Controller_Front_DispatchLoopShutdown',
            function () {
                $this->processQueue();
            }
        );
    }

    private function processQueue()
    {
        if (empty($this->queue)) {
            return;
        }
        $this->backlogProcessor->add($this->queue);
        $this->queue = [];
    }

    /**
     * @param object $entity
     *
     * @return Backlog
     */
    private function getDeleteBacklog($entity)
    {
        switch (true) {
            case $entity instanceof VariantModel:
                return new Backlog(self::EVENT_VARIANT_DELETED, ['number' => $entity->getNumber()]);
            case $entity instanceof PriceModel:
                return new Backlog(self::EVENT_PRICE_DELETED, ['number' => $entity->getDetail()->getNumber()]);
            case $entity instanceof VoteModel:
                return new Backlog(self::EVENT_VOTE_DELETED, ['numbers' => $this->getNumbers($entity->getArticle())]);
        }

        return null;
    }

    private function getInsertBacklog($entity)
    {
        switch (true) {
            case $entity instanceof VariantModel:
                return new Backlog(self::EVENT_VARIANT_INSERTED, ['number' => $entity->getNumber()]);
            case $entity instanceof PriceModel:
                return new Backlog(self::EVENT_PRICE_INSERTED, ['number' => $entity->getDetail()->getNumber()]);
            case $entity instanceof VoteModel:
                return new Backlog(self::EVENT_VOTE_INSERTED, ['numbers' => $this->getNumbers($entity->getArticle())]);
        }

        return null;
    }

    /**
     * @param object $entity
     *
     * @return Backlog
     */
    private function getUpdateBacklog($entity)
    {
        switch (true) {
            case $entity instanceof VariantModel:
                return new Backlog(self::EVENT_VARIANT_UPDATED, ['number' => $entity->getNumber()]);
            case $entity instanceof PriceModel:
                return new Backlog(self::EVENT_PRICE_UPDATED, ['number' => $entity->getDetail()->getNumber()]);
            case $entity instanceof VoteModel:
                return new Backlog(self::EVENT_VOTE_UPDATED, ['numbers' => $this->getNumbers($entity->getArticle())]);
            case $entity instanceof TaxModel:
                return new Backlog(self::EVENT_TAX_UPDATED, ['id' => $entity->getId()]);
            case $entity instanceof PropertyGroupModel:
                return new Backlog(self::EVENT_PROPERTY_GROUP_UPDATED, ['id' => $entity->getId()]);
            case $entity instanceof PropertyOptionModel:
                return new Backlog(self::EVENT_PROPERTY_OPTION_UPDATED, ['id' => $entity->getId(), 'groupId' => $entity->getOption()->getId()]);
        }

        return null;
    }

    /**
     * @param ArticleModel $entity
     * @return array
     */
    private function getNumbers(ArticleModel $entity)
    {
        $numbers = [];

        foreach ($entity->getDetails() as $detail) {
            $numbers[] = $detail->getNumber();
        }

        return $numbers;
    }
}
