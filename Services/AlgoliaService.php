<?php

namespace SwAlgolia\Services;

use AlgoliaSearch\Client;
use AlgoliaSearch\Index;
use Behat\Mink\Exception\Exception;
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

        // Init the index
        $index = $client->initIndex($indexName);

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
     * Initialize index
     * @param $indexName
     * @return Index|boolean
     */
    public function initIndex($indexName) {

        // Init the API client
        $client = new AlgoliaClient($this->pluginConfig['algolia-application-id'],$this->pluginConfig['algolia-admin-api-key']);
        $client->setConnectTimeout($this->pluginConfig['algolia-connection-timeout']);

        // Init the index
        try{
            $index = $client->initIndex($indexName);
            $this->logger->addDebug('Successfully initialized index {indexName}.',array('indexName' => $indexName));
            return $index;
        } catch (\Exception $e) {
            $this->logger->addDebug('Error while initializing index {indexName}: {errorMessage}.',array('indexName' => $indexName, 'errorMessage' => $e->getMessage()));
            return false;
        }

    }

    /**
     * Pushes the index settings from plugin configuration to the Algolia index
     * @param array $settings
     * @param Index|null $index
     * @param null $indexName
     * @return bool|mixed
     * @throws \Exception
     */
    public function pushIndexSettings(array $settings, Index $index=null, $indexName=null) {

        if(!$index && !$indexName) throw new \Exception('Either index or indexname has to be specified.');

        // Get the index if only the index name is passed
        if(!$index):
            $client = new AlgoliaClient($this->pluginConfig['algolia-application-id'],$this->pluginConfig['algolia-admin-api-key']);
            $client->setConnectTimeout($this->pluginConfig['algolia-connection-timeout']);
            $index = $client->initIndex($indexName);
        endif;

        // Push index settings to algolia
        try {
            $response = $index->setSettings($settings);
            $this->logger->addDebug('Successfully pushed index settings for index {indexName} to Algolia with TaskID {taskId}.',array('indexName' => $index->indexName, 'taskId' => $response['taskID']));
            return $response;
        } catch (\Exception $e) {
            $this->logger->addError('Error while pushing index settings to Algolia: {errorMessage}', array('errorMessage' => $e->getMessage()));
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

        // Delete index
        try {
            $client->deleteIndex($indexName);
            $this->logger->addDebug('Successfully deleted index {indexName}.', array('indexName' => $indexName));
            return true;
        } catch(\Exception $e) {
            $this->logger->addError('Error when trying to delete the index {indexName}: {errorMessage}', array('indexName' => $indexName, 'errorMessage' => $e->getMessage()));
            return false;
        }

    }



//    /**
//     * Creates the required replica indices at Algolia
//     * @param AlgoliaClient $client
//     * @param Index $index
//     */
//    private function createIndexReplicas(Client $client, Index $index) {
//
//        // Get the additional replica / sort indices
//        $replicaIndices = $this->getIndexReplicas($index);
//
//        // Iterate over all required replica indices
//        foreach($replicaIndices as $replicaIndexName => $replicaIndex):
//
//            // Push replica index to Algolia
//            try {
//
//                // Create replica indices
//                $response = $index->setSettings(array(
//                        'replicas' => array($replicaIndexName),
//                    )
//                );
//
//                // Wait for the task to be completed (to make sure replica indices are ready)
//                $index->waitTask($response['taskID']);
//
//                // Init and create the index
//                $client->initIndex($replicaIndexName)->setSettings(array(
//                    'ranking' => $replicaIndex
//                ));
//
//                $this->logger->addDebug('Successfully created replica index {replicaIndexName} with TaskID {taskId}.',array('replicaIndexName' => $replicaIndexName, 'taskId' => $response['taskID']));
//
//            } catch (\Exception $e) {
//                $this->logger->addError('Error while creating replica index {replicaIndexName}: {errorMessage}', array('replicaIndexName' => $replicaIndexName, 'errorMessage' => $e->getMessage()));
//            }
//
//        endforeach;
//
//    }


}