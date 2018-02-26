<?php

namespace SwAlgolia\Services;

use AlgoliaSearch\Client;
use AlgoliaSearch\Index;
use Exception;
use Shopware\Components\Logger;
use Shopware\Models\Shop\Shop;

/**
 * Class AlgoliaService.
 */
class AlgoliaService
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
     * Push a data array to an Algolia index.
     *
     * @param Shop $shop
     * @param array $data
     * @param $indexName
     *
     * @return bool
     * @throws \AlgoliaSearch\AlgoliaException
     */
    public function push(Shop $shop, array $data, $indexName)
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
     * Initialize index.
     *
     * @param $indexName
     *
     * @return Index|bool
     */
    public function initIndex($indexName)
    {
        try {
            $index = $this->algoliaClient->initIndex($indexName);
            $this->logger->addDebug('Successfully initialized index {indexName}.', ['indexName' => $indexName]);

            return $index;
        } catch (Exception $e) {
            $this->logger->addDebug('Error while initializing index {indexName}: {errorMessage}.', ['indexName' => $indexName, 'errorMessage' => $e->getMessage()]);

            return false;
        }
    }

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
    public function pushIndexSettings(array $settings, Index $index = null, $indexName = null)
    {
        // Get the index if only the index name is passed
        if (!$index) {
            $index = $this->algoliaClient->initIndex($indexName);
        }

        // Push index settings to algolia
        try {
            $response = $index->setSettings($settings);
            $this->logger->addDebug('Successfully pushed index settings for index {indexName} to Algolia with TaskID {taskId}.', ['indexName' => $index->indexName, 'taskId' => $response['taskID']]);

            return $response;
        } catch (Exception $e) {
            $this->logger->addError('Error while pushing index settings to Algolia: {errorMessage}', ['errorMessage' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Deletes an Algolia index and it´s replica indices by name.
     *
     * @param $indexName
     *
     * @return bool
     */
    public function deleteIndex($indexName)
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
