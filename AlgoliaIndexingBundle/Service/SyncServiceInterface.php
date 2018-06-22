<?php

namespace FroshAlgolia\AlgoliaIndexingBundle\Service;

interface SyncServiceInterface
{
    /**
     * Syncs complete article data to Angolia.
     *
     * @param array $shops
     * @return bool
     * @throws \AlgoliaSearch\AlgoliaException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Exception
     */
    public function fullSync(array $shops): bool;

    /**
     * This method consumes all events where entity data (e.g. articles) is changed and submits
     * the changed data on the fly to Algolia.
     *
     * @param array $numbers
     */
    public function liveSync(array $numbers): void;
}