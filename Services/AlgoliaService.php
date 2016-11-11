<?php

namespace SwAlgolia\Services;

use AlgoliaSearch\Client;
use AlgoliaSearch\Index;
use Shopware\Models\Shop\Shop;
use Shopware\Components;
use AlgoliaSearch\Client as AlgoliaClient;

/**
 * Class AlgoliaService
 * Wraps the AlgoliaSearch as a service.
 * @package SwAlgolia\Services
 */
class AlgoliaService
{

    /**
     * @var null|Components\Logger
     */
    private $logger = null;

    /**
     * @var null|array
     */
    private $pluginConfig = null;

    /**
     * AlgoliaService constructor.
     * @param Components\Logger $logger
     */
    public function __construct(Components\Logger $logger) {

        // Init Logger
        $this->logger = $logger;

        // Grab the plugin config
        $this->pluginConfig = Shopware()->Container()->get('shopware.plugin.cached_config_reader')->getByPluginName('SwAlgolia');

    }

    /**
     * Push a data array to an Algolia index
     * @param Shop $shop
     * @param array $data
     * @param $indexName
     * @return bool
     */
    public function push(Shop $shop, array $data, $indexName) {

        // Init the API client
        $client = new AlgoliaClient($this->pluginConfig['algolia-application-id'],$this->pluginConfig['algolia-admin-api-key']);
        $client->setConnectTimeout($this->pluginConfig['algolia-connection-timeout']);

        // Init the index and push index settings
        $index = $client->initIndex($indexName);

        // Create slave/replica indices for sort order
        $this->createIndexReplicas($client, $index, $shop);

        // Apply index settings
        $this->setIndexSettings($index);

        // Send data to algolia
        try {
            $response = $index->addObjects($data);
            $this->logger->addDebug('Successfully pushed elements {objectIds} for ShopId {shopId} to Algolia with TaskID {taskId}.', array('objectIds' => implode(', ',$response['objectIDs']), 'shopId' => $shop->getId(), 'taskId' => $response['taskID']));
            return true;
        } catch(\Exception $e) {
            $this->logger->addError('Error when pushing elements to Algolia: {errorMessage}', array('errorMessage' => $e->getMessage()));
            return false;
        }

    }

    /**
     * Deletes an Algolia index and it´s replica indices by name
     * @param $indexName
     * @return bool
     */
    public function deleteIndex($indexName) {

        $client = new AlgoliaClient($this->pluginConfig['algolia-application-id'],$this->pluginConfig['algolia-admin-api-key']);
        $client->setConnectTimeout($this->pluginConfig['algolia-connection-timeout']);
        $index = $client->initIndex($indexName);

        // Get the additional replica / sort indices
        // @TODO Is there a chance to retrieve the existing replica indices from algolia instead from the
        // @TODO plugin configuration? This would avoid data corruption in case of changed index settings
        // @TODO or changes on the algolia infrastructure.
        $replicaIndices = $this->getIndexReplicas($index);

        // Iterate over all replica indices
        foreach($replicaIndices as $replicaIndexName => $replicaIndex):

            // Push replica index to Algolia
            try {

                // Create replica indices
                $client->deleteIndex($replicaIndexName);
                $this->logger->addDebug('Successfully deleted replica index {replicaIndexName}.',array('replicaIndexName' => $replicaIndexName));

            } catch (\Exception $e) {
                $this->logger->addError('Error while creating replica index {replicaIndexName}: {errorMessage}', array('replicaIndexName' => $replicaIndexName, 'errorMessage' => $e->getMessage()));
            }

        endforeach;

        // Delete main index
        try {
            $index->deleteIndex();
            $this->logger->addDebug('Successfully deleted index {indexName}.', array('indexName' => $indexName));
            return true;
        } catch(\Exception $e) {
            $this->logger->addError('Error when trying to clear the index {indexName}: {errorMessage}', array('indexName' => $index, 'errorMessage' => $e->getMessage()));
            return false;
        }

    }

    /**
     * Pushes the index settings from plugin configuration to the Algolia index
     * @param Index $index
     * @return bool
     */
    public function setIndexSettings(Index $index) {

        // Push index settings to algolia
        try {
            $response = $index->setSettings(array(
                    'attributesToIndex' => explode(',',$this->pluginConfig['index-searchable-attributes']),
                    'customRanking' => explode(',', $this->pluginConfig['index-custom-ranking-attributes']),
                    'attributesForFaceting'  => explode(',', $this->pluginConfig['index-faceting-attributes']),
                )
            );
            $this->logger->addDebug('Successfully pushed index settings for index {indexName} to Algolia with TaskID {taskId}.',array('indexName' => $index->indexName, 'taskId' => $response['taskID']));
            return true;
        } catch (\Exception $e) {
            $this->logger->addError('Error while pushing index settings to Algolia: {errorMessage}', array('errorMessage' => $e->getMessage()));
            return false;
        }


    }

    /**
     * Creates the required replica indices at Algolia
     * @param AlgoliaClient $client
     * @param Index $index
     */
    public function createIndexReplicas(Client $client, Index $index) {

        // Get the additional replica / sort indices
        $replicaIndices = $this->getIndexReplicas($index);

        // Iterate over all required replica indices
        foreach($replicaIndices as $replicaIndexName => $replicaIndex):

            // Push replica index to Algolia
            try {

                // Create replica indices
                $response = $index->setSettings(array(
                        'replicas' => array($replicaIndexName),
                    )
                );

                // Wait for the task to be completed (to make sure replica indices are ready)
                $index->waitTask($response['taskID']);

                // Configure the replica indices
                $client->initIndex($replicaIndexName)->setSettings(array(
                    'ranking' => $replicaIndex
                ));

                $this->logger->addDebug('Successfully created replica index {replicaIndexName} with TaskID {taskId}.',array('replicaIndexName' => $replicaIndexName, 'taskId' => $response['taskID']));

            } catch (\Exception $e) {
                $this->logger->addError('Error while creating replica index {replicaIndexName}: {errorMessage}', array('replicaIndexName' => $replicaIndexName, 'errorMessage' => $e->getMessage()));
            }

        endforeach;

    }

    /**
     * Gets an array of replica indices for a given main index by configuration
     * @param Index $index
     * @return array
     */
    private function getIndexReplicas(Index $index) {

        $data = array();
        $replicaIndices = explode('|',$this->pluginConfig['index-replicas-custom-ranking-attributes']);

        foreach($replicaIndices as $replicaIndex):

            $replicaIndexElements = explode(',',$replicaIndex);

            // Build the key / name for the replica index
            $nameElements = explode('(',$replicaIndexElements[0]);
            $replicaIndexName = $index->indexName .'-'. rtrim($nameElements[1],')') . '-' . $nameElements[0];

            // Build the replicaindex array
            foreach($replicaIndexElements as $replicaIndexElement):
                $data[$replicaIndexName][] = $replicaIndexElement;
            endforeach;

        endforeach;

        return $data;

    }

}