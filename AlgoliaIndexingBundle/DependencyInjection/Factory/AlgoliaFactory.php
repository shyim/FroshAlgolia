<?php declare(strict_types=1);

namespace FroshAlgolia\AlgoliaIndexingBundle\DependencyInjection\Factory;

use AlgoliaSearch\Client;
use Shopware\Components\Plugin\CachedConfigReader;

/**
 * Class AlgoliaFactory.
 */
class AlgoliaFactory
{
    /**
     * @param array $pluginConfig
     *
     * @return Client
     * @throws \AlgoliaSearch\AlgoliaException
     */
    public static function factory(array $pluginConfig)
    {
        $client = new Client($pluginConfig['algolia-application-id'], $pluginConfig['algolia-admin-api-key']);
        $client->setConnectTimeout($pluginConfig['algolia-connection-timeout']);

        return $client;
    }
}
