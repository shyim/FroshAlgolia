<?php declare(strict_types=1);

namespace FroshAlgolia\AlgoliaIndexingBundle\Service;

use Shopware\Models\Shop\Shop;

interface SyncServiceInterface
{
    /**
     * Syncs complete article data to Angolia.
     *
     * @param array $shops
     *
     * @throws \AlgoliaSearch\AlgoliaException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Exception
     *
     * @return bool
     */
    public function fullSync(array $shops): bool;

    /**
     * This method consumes all events where entity data (e.g. articles) is changed and submits
     * the changed data on the fly to Algolia.
     *
     * @param Shop[] $shops
     * @param array  $updateNumbers
     * @param array  $deleteNumbers
     */
    public function liveSync(array $shops, array $updateNumbers, array $deleteNumbers): void;
}
