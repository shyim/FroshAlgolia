<?php

namespace SwAlgolia\Services;

interface BacklogReaderInterface
{
    /**
     * @param int $lastId
     * @param int $limit
     * @return SwAlgolia\Structs\Backlog[]
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
