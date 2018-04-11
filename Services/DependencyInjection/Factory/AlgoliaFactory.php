<?php

namespace FroshAlgolia\Services\DependencyInjection\Factory;

use AlgoliaSearch\Client;
use Shopware\Components\Plugin\CachedConfigReader;

/**
 * Class AlgoliaFactory.
 */
class AlgoliaFactory
{
    /**
     * @param CachedConfigReader $configReader
     *
     * @return Client
     * @throws \AlgoliaSearch\AlgoliaException
     * @throws \Exception
     */
    public static function factory(CachedConfigReader $configReader)
    {
        $config = $configReader->getByPluginName('FroshAlgolia');

        $client = new Client($config['algolia-application-id'], $config['algolia-admin-api-key']);
        $client->setConnectTimeout($config['algolia-connection-timeout']);

        return $client;
    }
}
