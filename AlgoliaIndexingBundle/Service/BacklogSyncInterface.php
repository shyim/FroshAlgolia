<?php

namespace FroshAlgolia\AlgoliaIndexingBundle\Service;

interface BacklogSyncInterface
{
    /**
     * @param int $limit
     * @return void
     */
    public function sync(int $limit = 100) :void;
}