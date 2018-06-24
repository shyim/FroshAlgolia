<?php declare(strict_types=1);

namespace FroshAlgolia\AlgoliaIndexingBundle\Service\Core;

use AlgoliaSearch\Client;
use AlgoliaSearch\Index;
use Exception;
use FroshAlgolia\AlgoliaIndexingBundle\Service\AlgoliaServiceInterface;
use Shopware\Components\Logger;
use Shopware\Models\Shop\Shop;

/**
 * Class AlgoliaService.
 */
class AlgoliaService implements AlgoliaServiceInterface
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Client
     */
    private $algoliaClient;

    /**
     * AlgoliaService constructor.
     *
     * @param Client $client
     * @param Logger $logger
     */
    public function __construct(
        Client $client,
        Logger $logger
    ) {
        $this->logger = $logger;
        $this->algoliaClient = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function push(Shop $shop, array $data, $indexName): bool
    {
        // Init the index
        $index = $this->algoliaClient->initIndex($indexName);

        // Send data to algolia
        try {
            $response = $index->addObjects($data);
            $this->logger->addDebug('Successfully pushed elements {objectIds} for ShopId {shopId} to Algolia with TaskID {taskId}.', ['objectIds' => implode(', ', $response['objectIDs']), 'shopId' => $shop->getId(), 'taskId' => $response['taskID']]);

            return true;
        } catch (Exception $e) {
            $this->logger->addError('Error when pushing elements to Algolia: {errorMessage}', ['errorMessage' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function update(Shop $shop, array $data, string $indexName): bool
    {
        // Init the index
        $index = $this->algoliaClient->initIndex($indexName);

        try {
            $response = $index->saveObjects($data);
            $this->logger->addDebug('Successfully pushed elements {objectIds} for ShopId {shopId} to Algolia with TaskID {taskId}.', ['objectIds' => implode(', ', $response['objectIDs']), 'shopId' => $shop->getId(), 'taskId' => $response['taskID']]);

            return true;
        } catch (Exception $e) {
            $this->logger->addError('Error when pushing elements to Algolia: {errorMessage}', ['errorMessage' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Shop $shop, array $data, string $indexName): bool
    {
        // Init the index
        $index = $this->algoliaClient->initIndex($indexName);

        try {
            $response = $index->deleteObjects($data);
            $this->logger->addDebug('Successfully deleted elements {objectIds} for ShopId {shopId} to Algolia with TaskID {taskId}.', ['objectIds' => implode(', ', $response['objectIDs']), 'shopId' => $shop->getId(), 'taskId' => $response['taskID']]);

            return true;
        } catch (Exception $e) {
            $this->logger->addError('Error when deleting elements on Algolia: {errorMessage}', ['errorMessage' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function initIndex($indexName): Index
    {
        $index = $this->algoliaClient->initIndex($indexName);
        $this->logger->addDebug('Successfully initialized index {indexName}.', ['indexName' => $indexName]);

        return $index;
    }

    /**
     * {@inheritdoc}
     */
    public function pushIndexSettings(array $settings, Index $index = null, $indexName = null): array
    {
        // Get the index if only the index name is passed
        if (!$index) {
            $index = $this->algoliaClient->initIndex($indexName);
        }

        // Push index settings to algolia
        $response = $index->setSettings($settings);
        $this->logger->addDebug('Successfully pushed index settings for index {indexName} to Algolia with TaskID {taskId}.', ['indexName' => $index->indexName, 'taskId' => $response['taskID']]);

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteIndex($indexName): bool
    {
        try {
            $this->algoliaClient->deleteIndex($indexName);
            $this->logger->addDebug('Successfully deleted index {indexName}.', ['indexName' => $indexName]);

            return true;
        } catch (Exception $e) {
            $this->logger->addError('Error when trying to delete the index {indexName}: {errorMessage}', ['indexName' => $indexName, 'errorMessage' => $e->getMessage()]);

            return false;
        }
    }
}
