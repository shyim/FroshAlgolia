<?php

namespace SwAlgolia\Services;

use AlgoliaSearch\Client;

/**
 * Class ClientFactory.
 */
class ClientFactory
{
    /**
     * @return Client
     */
    public static function createClient()
    {
        $pluginConfig = Shopware()->Container()->get('shopware.plugin.cached_config_reader')->getByPluginName('SwAlgolia');
        $client = new Client($pluginConfig['algolia-application-id'], $pluginConfig['algolia-admin-api-key']);

        return $client;
    }
}
