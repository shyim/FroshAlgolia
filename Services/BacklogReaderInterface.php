<?php

namespace SwAlgolia\Services;
use SwAlgolia\Structs\Backlog;

interface BacklogReaderInterface
{
    /**
     * @param int $lastId
     * @param int $limit
     * @return Backlog[]
     */
    public function read($lastId, $limit);

    /**
     * @return int
     */
    public function getLastBacklogId();

    /**
     * @param int $lastId
     */
    public function setLastBacklogId($lastId);
}
