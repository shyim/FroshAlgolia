<?php declare(strict_types=1);

namespace FroshAlgolia\AlgoliaIndexingBundle\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use FroshAlgolia\AlgoliaIndexingBundle\Service\BacklogProcessorInterface;
use FroshAlgolia\Structs\Backlog;
use Shopware\Components\ContainerAwareEventManager;
use Shopware\Components\DependencyInjection\Container;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Article\Article as ArticleModel;
use Shopware\Models\Article\Detail as VariantModel;
use Shopware\Models\Article\Price as PriceModel;
use Shopware\Models\Article\Supplier as SupplierModel;
use Shopware\Models\Article\Unit as UnitModel;
use Shopware\Models\Article\Vote as VoteModel;
use Shopware\Models\Property\Option as PropertyGroupModel;
use Shopware\Models\Property\Value as PropertyOptionModel;
use Shopware\Models\Tax\Tax as TaxModel;

/**
 * Class ORMBacklogSubscriber.
 */
class ORMBacklogSubscriber implements EventSubscriber
{
    public const EVENT_ARTICLE_DELETED = 'article_deleted';
    public const EVENT_ARTICLE_INSERTED = 'article_inserted';
    public const EVENT_ARTICLE_UPDATED = 'article_updated';
    public const EVENT_VARIANT_DELETED = 'variant_deleted';
    public const EVENT_VARIANT_INSERTED = 'variant_inserted';
    public const EVENT_VARIANT_UPDATED = 'variant_updated';
    public const EVENT_PRICE_DELETED = 'variant_price_deleted';
    public const EVENT_PRICE_INSERTED = 'variant_price_inserted';
    public const EVENT_PRICE_UPDATED = 'variant_price_updated';
    public const EVENT_VOTE_DELETED = 'vote_deleted';
    public const EVENT_VOTE_INSERTED = 'vote_inserted';
    public const EVENT_VOTE_UPDATED = 'vote_updated';
    public const EVENT_SUPPLIER_DELETED = 'supplier_deleted';
    public const EVENT_SUPPLIER_INSERTED = 'supplier_inserted';
    public const EVENT_SUPPLIER_UPDATED = 'supplier_updated';
    public const EVENT_TAX_DELETED = 'tax_deleted';
    public const EVENT_TAX_INSERTED = 'tax_inserted';
    public const EVENT_TAX_UPDATED = 'tax_updated';
    public const EVENT_UNIT_DELETED = 'article_unit_deleted';
    public const EVENT_UNIT_INSERTED = 'article_unit_inserted';
    public const EVENT_UNIT_UPDATED = 'article_unit_updated';
    public const EVENT_PROPERTY_GROUP_DELETED = 'property_group_deleted';
    public const EVENT_PROPERTY_GROUP_INSERTED = 'property_group_inserted';
    public const EVENT_PROPERTY_GROUP_UPDATED = 'property_group_updated';
    public const EVENT_PROPERTY_OPTION_DELETED = 'property_option_deleted';
    public const EVENT_PROPERTY_OPTION_INSERTED = 'property_option_inserted';
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
     * @param ContainerAwareEventManager $eventsManager
     * @param BacklogProcessorInterface $backlogProcessor
     */
    public function __construct(ContainerAwareEventManager $eventsManager, BacklogProcessorInterface $backlogProcessor)
    {
        $this->eventsManager = $eventsManager;
        $this->backlogProcessor = $backlogProcessor;
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
            case $entity instanceof ArticleModel:
                return new Backlog(self::EVENT_ARTICLE_DELETED, ['id' => $entity->getId()]);
            case $entity instanceof VariantModel:
                return new Backlog(self::EVENT_VARIANT_DELETED, ['number' => $entity->getNumber()]);
            case $entity instanceof PriceModel:
                return new Backlog(self::EVENT_PRICE_DELETED, ['number' => $entity->getDetail()->getNumber()]);
            case $entity instanceof VoteModel:
                return new Backlog(self::EVENT_VOTE_DELETED, ['articleId' => $entity->getArticle()->getId()]);
            case $entity instanceof SupplierModel:
                return new Backlog(self::EVENT_SUPPLIER_DELETED, ['id' => $entity->getId()]);
            case $entity instanceof UnitModel:
                return new Backlog(self::EVENT_UNIT_DELETED, ['id' => $entity->getId()]);
            case $entity instanceof TaxModel:
                return new Backlog(self::EVENT_TAX_DELETED, ['id' => $entity->getId()]);
            case $entity instanceof PropertyGroupModel:
                return new Backlog(self::EVENT_PROPERTY_GROUP_DELETED, ['id' => $entity->getId()]);
            case $entity instanceof PropertyOptionModel:
                return new Backlog(self::EVENT_PROPERTY_OPTION_DELETED, ['id' => $entity->getId(), 'groupId' => $entity->getOption()->getId()]);
        }

        return null;
    }

    private function getInsertBacklog($entity)
    {
        switch (true) {
            case $entity instanceof ArticleModel:
                return new Backlog(self::EVENT_ARTICLE_INSERTED, ['id' => $entity->getId()]);
            case $entity instanceof VariantModel:
                return new Backlog(self::EVENT_VARIANT_INSERTED, ['number' => $entity->getNumber()]);
            case $entity instanceof PriceModel:
                return new Backlog(self::EVENT_PRICE_INSERTED, ['number' => $entity->getDetail()->getNumber()]);
            case $entity instanceof VoteModel:
                return new Backlog(self::EVENT_VOTE_INSERTED, ['articleId' => $entity->getArticle()->getId()]);
            case $entity instanceof SupplierModel:
                return new Backlog(self::EVENT_SUPPLIER_INSERTED, ['id' => $entity->getId()]);
            case $entity instanceof UnitModel:
                return new Backlog(self::EVENT_UNIT_INSERTED, ['id' => $entity->getId()]);
            case $entity instanceof TaxModel:
                return new Backlog(self::EVENT_TAX_INSERTED, ['id' => $entity->getId()]);
            case $entity instanceof PropertyGroupModel:
                return new Backlog(self::EVENT_PROPERTY_GROUP_INSERTED, ['id' => $entity->getId()]);
            case $entity instanceof PropertyOptionModel:
                return new Backlog(self::EVENT_PROPERTY_OPTION_INSERTED, ['id' => $entity->getId(), 'groupId' => $entity->getOption()->getId()]);
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
            case $entity instanceof ArticleModel:
                return new Backlog(self::EVENT_ARTICLE_UPDATED, ['id' => $entity->getId()]);
            case $entity instanceof VariantModel:
                return new Backlog(self::EVENT_VARIANT_UPDATED, ['number' => $entity->getNumber()]);
            case $entity instanceof PriceModel:
                return new Backlog(self::EVENT_PRICE_UPDATED, ['number' => $entity->getDetail()->getNumber()]);
            case $entity instanceof VoteModel:
                return new Backlog(self::EVENT_VOTE_UPDATED, ['articleId' => $entity->getArticle()->getId()]);
            case $entity instanceof SupplierModel:
                return new Backlog(self::EVENT_SUPPLIER_UPDATED, ['id' => $entity->getId()]);
            case $entity instanceof UnitModel:
                return new Backlog(self::EVENT_UNIT_UPDATED, ['id' => $entity->getId()]);
            case $entity instanceof TaxModel:
                return new Backlog(self::EVENT_TAX_UPDATED, ['id' => $entity->getId()]);
            case $entity instanceof PropertyGroupModel:
                return new Backlog(self::EVENT_PROPERTY_GROUP_UPDATED, ['id' => $entity->getId()]);
            case $entity instanceof PropertyOptionModel:
                return new Backlog(self::EVENT_PROPERTY_OPTION_UPDATED, ['id' => $entity->getId(), 'groupId' => $entity->getOption()->getId()]);
        }

        return null;
    }
}
