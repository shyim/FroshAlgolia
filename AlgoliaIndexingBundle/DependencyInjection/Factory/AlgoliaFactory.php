<?php declare(strict_types=1);

namespace FroshAlgolia\AlgoliaIndexingBundle\DependencyInjection\Factory;

use AlgoliaSearch\Client;

/**
 * Class AlgoliaFactory.
 */
class AlgoliaFactory
{
    /**
     * @param array $pluginConfig
     *
     * @throws \AlgoliaSearch\AlgoliaException
     *
     * @return Client
     */
    public static function factory(array $pluginConfig)
    {
        $client = new Client($pluginConfig['algolia-application-id'], $pluginConfig['algolia-admin-api-key']);
        $client->setConnectTimeout($pluginConfig['algolia-connection-timeout']);

        return $client;
    }
}
