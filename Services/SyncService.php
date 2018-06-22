<?php

namespace FroshAlgolia\Services;

use Doctrine\ORM\EntityManager;
use Shopware\Components\Logger;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Shop\Shop;
use FroshAlgolia\Structs\Struct;

/**
 * Class SyncService.
 *
 * Todo: CLEANUPPPP
 */
class SyncService
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var ProductIndexer
     */
    private $productIndexer;

    /**
     * @var AlgoliaService
     */
    private $algoliaService;

    /**
     * @var SyncHelperService
     */
    private $syncHelperService;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var ConfigReader
     */
    private $configReader;

    /**
     * @var array
     */
    private $pluginConfig;

    /**
     * @var array
     */
    private $shopConfig = [];

    /**
     * SyncService constructor.
     * @param ProductIndexer $productIndexer
     * @param AlgoliaService $algoliaService
     * @param ConfigReader $configReader
     * @param ModelManager $modelManager
     * @param array $pluginConfig
     */
    public function __construct(
        ProductIndexer $productIndexer,
        AlgoliaService $algoliaService,
        ConfigReader $configReader,
        ModelManager $modelManager,
        array $pluginConfig
    ) {
        $this->productIndexer = $productIndexer;
        $this->algoliaService = $algoliaService;
        $this->em = $modelManager;
        $this->configReader = $configReader;
        $this->pluginConfig = $pluginConfig;
    }

    /**
     * Syncs complete article data to Angolia.
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function fullSync()
    {
        // Get all shops
        if (!$shops = $this->getShops()) {
            throw new \Exception('No active shop found.');
        }

        // Iterate over all shops
        /** @var Shop $shop */
        foreach ($shops as $shop) {
            $this->shopConfig = $this->configReader->read($shop);

            $shop->registerResources();
            $this->deleteIndex($shop);
            $this->createIndices($shop);

            $productChunks = $this->productIndexer->index($shop, $this->pluginConfig['sync-batch-size'], $this->shopConfig);

            foreach ($productChunks as $productChunk) {
                $this->algoliaService->push($shop, $productChunk, $this->buildIndexName($shop));
            }
        }

        return true;
    }

    /**
     * This method consumes all events where entity data (e.g. articles) is changed and submits
     * the changed data on the fly to Algolia.
     *
     * @param Struct $product
     */
    public function liveSync(Struct $product)
    {
        // @TODO TBD
    }

    /**
     * Creates and inits all indices and replica indices for a given shop.
     *
     * @param $shop
     */
    private function createIndices($shop)
    {
        // Create main index
        $indexName = $this->buildIndexName($shop);
        $index = $this->algoliaService->initIndex($indexName);

        $attributesForFaceting = array_column($this->shopConfig['facetAttributes'], 'name');

        // Create indices, replica indices and define settings
        $indexSettings = [
            'attributesToIndex' => array_column($this->shopConfig['searchAttributes'], 'name'),
            'customRanking' => $this->configReader->convertToAlgoliaRanking($this->shopConfig['rankingIndexAttributes']),
            'attributesForFaceting' => $attributesForFaceting,
            'replicas' => $this->getReplicaNames($indexName),
        ];
        $settingsResponse = $this->algoliaService->pushIndexSettings($indexSettings, $index);

        // Wait for the task to be completed (to make sure replica indices are ready)
        $index->waitTask($settingsResponse['taskID']);

        // Define replica settings
        $replicaIndices = explode('|', $this->pluginConfig['index-replicas-custom-ranking-attributes']);

        foreach ($replicaIndices as $replicaIndex) {
            $replicaIndexSettings = explode(',', $replicaIndex);

            // Build the key / name for the replica index
            $nameElements = explode('(', $replicaIndexSettings[0]);
            $replicaIndexName = $indexName.'_'.rtrim($nameElements[1], ')').'_'.$nameElements[0];

            $params = [
                'ranking' => $replicaIndexSettings,
                'attributesForFaceting' => $attributesForFaceting,
            ];

            $this->algoliaService->pushIndexSettings($params, null, $replicaIndexName);
        }
    }

    /**
     * Gets an array of all replica indices that needs to be created for a main index.
     *
     * @param $indexName
     *
     * @return array
     */
    private function getReplicaNames($indexName)
    {
        $names = [];

        // Get the replicas from config
        $replicaIndices = explode('|', $this->pluginConfig['index-replicas-custom-ranking-attributes']);

        foreach ($replicaIndices as $replicaIndex) {
            $replicaIndexElements = explode(',', $replicaIndex);

            // Build the key / name for the replica index
            $nameElements = explode('(', $replicaIndexElements[0]);
            $replicaIndexName = $indexName.'_'.rtrim($nameElements[1], ')').'_'.$nameElements[0];

            $names[] = $replicaIndexName;
        }

        return $names;
    }

    /**
     * Deletes all indices for a shop.
     *
     * @param Shop $shop
     */
    private function deleteIndex(Shop $shop)
    {
        $indexName = $this->buildIndexName($shop);

        // Delete main index
        $this->algoliaService->deleteIndex($indexName);
    }

    /**
     * @return array
     */
    private function getShops()
    {
        return $this->em->getRepository(Shop::class)->findBy(['active' => true]);
    }

    /**
     * @param Shop $shop
     * @return string
     */
    private function buildIndexName(Shop $shop)
    {
        $prefix = isset($this->pluginConfig['index-prefix-name']) && $this->pluginConfig['index-prefix-name'] != '' ? $this->pluginConfig['index-prefix-name'].'_' : false;

        return $prefix.$shop->getId();
    }
}
