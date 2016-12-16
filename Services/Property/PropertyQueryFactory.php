<?php

namespace SwAlgolia\Services\Property;

use Doctrine\DBAL\Connection;
use SwAlgolia\Services\LastIdQuery;

/**
 * Class PropertyQueryFactory
 * @package SwAlgolia\Services\Property
 */
class PropertyQueryFactory
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
     * @return LastIdQuery
     */
    public function createQuery()
    {
        return new LastIdQuery($this->createOptionQuery());
    }

    /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    private function createOptionQuery()
    {
        $query = $this->connection->createQueryBuilder();
        $query->select(['propertyGroups.id', 'propertyGroups.id'])
            ->from('s_filter_options', 'propertyGroups')
            ->where('propertyGroups.id > :lastId')
            ->setParameter(':lastId', 0);
        return $query;
    }
}
