<?php declare(strict_types=1);

namespace FroshAlgolia\AlgoliaIndexingBundle\Service\Core;

use FroshAlgolia\AlgoliaBundle\Service\ConfigReaderInterface;
use FroshAlgolia\AlgoliaBundle\Service\IndexNameBuilderInterface;
use FroshAlgolia\AlgoliaIndexingBundle\Service\AlgoliaServiceInterface;
use FroshAlgolia\AlgoliaIndexingBundle\Service\ProductIndexerInterface;
use FroshAlgolia\AlgoliaIndexingBundle\Service\SyncServiceInterface;
use Shopware\Models\Shop\Shop;

/**
 * Class SyncService.
 */
class SyncService implements SyncServiceInterface
{
    /**
     * @var ProductIndexerInterface
     */
    private $productIndexer;

    /**
     * @var AlgoliaServiceInterface
     */
    private $algoliaService;

    /**
     * @var ConfigReaderInterface
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
     * @var IndexNameBuilderInterface
     */
    private $indexNameBuilder;

    /**
     * SyncService constructor.
     *
     * @param ProductIndexerInterface $productIndexer
     * @param AlgoliaServiceInterface $algoliaService
     * @param ConfigReaderInterface $configReader
     * @param IndexNameBuilderInterface $indexNameBuilder
     * @param array $pluginConfig
     */
    public function __construct(
        ProductIndexerInterface $productIndexer,
        AlgoliaServiceInterface $algoliaService,
        ConfigReaderInterface $configReader,
        IndexNameBuilderInterface $indexNameBuilder,
        array $pluginConfig
    )
    {
        $this->productIndexer = $productIndexer;
        $this->algoliaService = $algoliaService;
        $this->configReader = $configReader;
        $this->pluginConfig = $pluginConfig;
        $this->indexNameBuilder = $indexNameBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function fullSync(array $shops): bool
    {
        // Iterate over all shops
        /** @var Shop $shop */
        foreach ($shops as $shop) {
            $this->shopConfig = $this->configReader->read($shop);
            $indexName = $this->indexNameBuilder->buildName($shop);

            $shop->registerResources();
            $this->deleteIndex($indexName);
            $this->createIndices($indexName);

            $productChunks = $this->productIndexer->index($shop, $this->pluginConfig['sync-batch-size'], $this->shopConfig);

            foreach ($productChunks as $productChunk) {
                $this->algoliaService->push($shop, $productChunk, $indexName);
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function liveSync(array $shops, array $updateNumbers, array $deleteNumbers): void
    {
        /** @var Shop $shop */
        foreach ($shops as $shop) {
            $shop->registerResources();
            $this->shopConfig = $this->configReader->read($shop);
            $indexName = $this->indexNameBuilder->buildName($shop);

            if (!empty($updateNumbers)) {
                $products = $this->productIndexer->indexNumbers($updateNumbers, $shop, $this->shopConfig);
                $receivedProducts = array_column($products, 'objectID');

                $deleteNumbers = array_unique(array_merge(array_diff($updateNumbers, $receivedProducts), $deleteNumbers));

                $this->algoliaService->update($shop, $products, $indexName);
            }

            if (!empty($deleteNumbers)) {
                $this->algoliaService->delete($shop, $deleteNumbers, $indexName);
            }
        }

    }

    /**
     * Creates and inits all indices and replica indices for a given shop.
     *
     * @param string $indexName
     *
     * @throws \Exception
     */
    private function createIndices(string $indexName): void
    {
        // Create main index
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
            $replicaIndexName = $indexName . '_' . rtrim($nameElements[1], ')') . '_' . $nameElements[0];

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
    private function getReplicaNames($indexName): array
    {
        $names = [];

        // Get the replicas from config
        $replicaIndices = explode('|', $this->pluginConfig['index-replicas-custom-ranking-attributes']);

        foreach ($replicaIndices as $replicaIndex) {
            $replicaIndexElements = explode(',', $replicaIndex);

            // Build the key / name for the replica index
            $nameElements = explode('(', $replicaIndexElements[0]);
            $replicaIndexName = $indexName . '_' . rtrim($nameElements[1], ')') . '_' . $nameElements[0];

            $names[] = $replicaIndexName;
        }

        return $names;
    }

    /**
     * Deletes all indices for a shop.
     *
     * @param string $indexName
     */
    private function deleteIndex(string $indexName): void
    {
        $this->algoliaService->deleteIndex($indexName);
    }
}
