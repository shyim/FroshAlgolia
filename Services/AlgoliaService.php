<?php

namespace SwAlgolia\Services;

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
     * @param $index
     * @return bool
     */
    public function push(Shop $shop, array $data, $index) {

        // Init the API client
        $client = new AlgoliaClient($this->pluginConfig['algolia-application-id'],$this->pluginConfig['algolia-admin-api-key']);
        $client->setConnectTimeout($this->pluginConfig['algolia-connection-timeout']);

        $index = $client->initIndex($index);

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

}