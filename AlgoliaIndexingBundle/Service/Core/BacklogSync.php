<?php

namespace FroshAlgolia\AlgoliaIndexingBundle\Service\Core;

use Doctrine\DBAL\Connection;
use FroshAlgolia\AlgoliaIndexingBundle\Service\BacklogSyncInterface;
use FroshAlgolia\AlgoliaIndexingBundle\Service\SyncServiceInterface;
use FroshAlgolia\AlgoliaIndexingBundle\Subscriber\ORMBacklogSubscriber;
use PDO;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Shop\Shop;

class BacklogSync implements BacklogSyncInterface
{
    /**
     * @var ModelManager
     */
    private $models;

    /**
     * @var SyncServiceInterface
     */
    private $syncService;

    /**
     * BacklogSync constructor.
     * @param ModelManager $models
     * @param SyncServiceInterface $syncService
     */
    public function __construct(ModelManager $models, SyncServiceInterface $syncService)
    {
        $this->models = $models;
        $this->syncService = $syncService;
    }

    /**
     * {@inheritdoc}
     */
    public function sync(int $limit = 100) : void
    {
        $entries = $this->models->getConnection()->fetchAll('SELECT * FROM FroshAlgolia_backlog LIMIT ' . $limit);

        [$updateNumbers, $deleteNumbers] = $this->getProductNumbersFromEntries($entries);

        $shops = $this->models->getRepository(Shop::class)->getActiveShops();

        $this->syncService->liveSync($shops, $updateNumbers, $deleteNumbers);

        $qb = $this->models->getConnection()->createQueryBuilder();
        $qb->delete('FroshAlgolia_backlog')
            ->where('id IN(:ids)')
            ->setParameter('ids', array_column($entries, 'id'), Connection::PARAM_INT_ARRAY)
            ->execute();
    }

    /**
     * @param array $entries
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    private function getProductNumbersFromEntries(array $entries)
    {
        $updateNumbers = [];
        $deleteNumbers = [];
        $connection = $this->models->getConnection();

        foreach ($entries as $entry) {
            $entry['payload'] = json_decode($entry['payload'], true);

            if (isset($entry['payload']['number'])) {
                $entry['payload']['numbers'] = [$entry['payload']['number']];
            }

            if (isset($entry['payload']['numbers'])) {
                if (strpos($entry['event'], 'deleted') !== false) {
                    $deleteNumbers = array_merge($entry['payload']['numbers'], $deleteNumbers);
                } else {
                    $updateNumbers = array_merge($entry['payload']['numbers'], $updateNumbers);
                }
                continue;
            }

            switch ($entry['event']) {
                case ORMBacklogSubscriber::EVENT_TAX_UPDATED:
                    $updateNumbers += $connection->executeQuery('SELECT ordernumber FROM s_articles_details WHERE articleID IN(SELECT id FROM s_articles WHERE taxId = ?)', [
                        $entry['payload']['id']
                    ])->fetchAll(PDO::FETCH_COLUMN);
                    continue;

            }
        }

        return [array_unique($updateNumbers), array_unique($deleteNumbers)];
    }
}