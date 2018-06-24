<?php declare(strict_types=1);

namespace FroshAlgolia\AlgoliaIndexingBundle\Service;

use AlgoliaSearch\Index;
use Exception;
use Shopware\Models\Shop\Shop;

interface AlgoliaServiceInterface
{
    /**
     * Push a data array to an Algolia index.
     *
     * @param Shop  $shop
     * @param array $data
     * @param $indexName
     *
     * @throws \AlgoliaSearch\AlgoliaException
     *
     * @return bool
     */
    public function push(Shop $shop, array $data, $indexName): bool;

    /**
     * @param Shop   $shop
     * @param array  $data
     * @param string $indexName
     *
     * @return bool
     */
    public function update(Shop $shop, array $data, string $indexName): bool;

    /**
     * @param Shop   $shop
     * @param array  $data
     * @param string $indexName
     *
     * @return bool
     */
    public function delete(Shop $shop, array $data, string $indexName): bool;

    /**
     * Initialize index.
     *
     * @param $indexName
     *
     * @return Index|bool
     */
    public function initIndex($indexName): Index;

    /**
     * Pushes the index settings from plugin configuration to the Algolia index.
     *
     * @param array      $settings
     * @param Index|null $index
     * @param string     $indexName
     *
     * @throws Exception
     *
     * @return bool
     */
    public function pushIndexSettings(array $settings, Index $index = null, $indexName = null): array;

    /**
     * Deletes an Algolia index and it´s replica indices by name.
     *
     * @param $indexName
     *
     * @return bool
     */
    public function deleteIndex($indexName): bool;
}
