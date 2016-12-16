<?php

namespace SwAlgolia\Services;

use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Class LastIdQuery
 * @package SwAlgolia\Services
 */
class LastIdQuery
{
    /**
     * @var QueryBuilder
     */
    private $query;

    /**
     * @param QueryBuilder $query
     */
    public function __construct(QueryBuilder $query)
    {
        $this->query = $query;
    }

    /**
     * @return array
     */
    public function fetch()
    {
        $data = $this->query->execute()->fetchAll(\PDO::FETCH_KEY_PAIR);
        $keys = array_keys($data);
        $this->query->setParameter(':lastId', array_pop($keys));
        return array_values($data);
    }

    /**
     * @return int
     */
    public function fetchCount()
    {
        /**@var $query QueryBuilder*/
        $query = clone $this->query;

        //get first column for distinct selection
        $select = $query->getQueryPart('select');

        $query->resetQueryPart('orderBy');
        $query->select('COUNT(DISTINCT '. array_shift($select) .')');

        return $query->execute()->fetch(\PDO::FETCH_COLUMN);
    }

    /**
     * @return QueryBuilder
     */
    public function getQuery()
    {
        return $this->query;
    }
}
