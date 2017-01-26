<?php

namespace SwAlgolia\Services;

use Doctrine\DBAL\Connection;
use SwAlgolia\Structs\Backlog;

/**
 * Class BacklogReader.
 */
class BacklogReader implements BacklogReaderInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastBacklogId()
    {
        $value = $this->connection->createQueryBuilder()
            ->select('value')
            ->from('s_core_config_elements', 'elements')
            ->where('elements.name = :name')
            ->setParameter(':name', 'lastSwalgoliaBacklogId')
            ->setMaxResults(1)
            ->execute()
            ->fetch(\PDO::FETCH_COLUMN);

        return unserialize($value);
    }

    /**
     * {@inheritdoc}
     */
    public function setLastBacklogId($lastId)
    {
        $this->connection->executeUpdate(
            "UPDATE s_core_config_elements SET value = :value WHERE name = 'lastSwalgoliaBacklogId'",
            [':value' => serialize($lastId)]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function read($lastId, $limit)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select(['id', 'event', 'payload', 'time'])
            ->from('swalgolia_backlog', 'backlog')
            ->andWhere('backlog.id > :lastId')
            ->orderBy('backlog.id', 'ASC')
            ->setParameter(':lastId', $lastId)
            ->setMaxResults($limit);

        $data = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);

        $result = [];
        foreach ($data as $row) {
            $backlog = new Backlog(
                $row['event'],
                json_decode($row['payload'], true),
                $row['time'],
                (int) $row['id']
            );
            $result[] = $backlog;
        }

        return $result;
    }
}
