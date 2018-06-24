<?php declare(strict_types=1);

namespace FroshAlgolia\AlgoliaIndexingBundle\Service;

interface BacklogSyncInterface
{
    /**
     * @param int $limit
     */
    public function sync(int $limit = 100): void;
}
