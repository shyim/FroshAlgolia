<?php

namespace SwAlgolia\Services;

use Shopware\Components;
use Shopware\Models\Shop\Shop;

/**
 * Class SyncHelperService
 * @package SwAlgolia\Services
 */
class SyncHelperService
{

    /**
     * @var Components\Logger
     */
    private $logger;

    /**
     * @var array
     */
    private $pluginConfig;

    public function __construct(Components\Logger $logger)
    {
        $this->logger = $logger;

        // Grab the plugin config
        $this->pluginConfig = Shopware()->Container()->get('shopware.plugin.cached_config_reader')->getByPluginName('SwAlgolia');
    }

    /**
     * Build the main index name for a shop. The Index name consists of the prefix set in the
     * plugin configuration and the shop ID.
     * @param Shop $shop
     * @return string
     */
    public function buildIndexName(Shop $shop)
    {
        $prefix = isset($this->pluginConfig['index-prefix-name']) && $this->pluginConfig['index-prefix-name']!='' ? $this->pluginConfig['index-prefix-name'] .'_' : false;
        return $prefix . $shop->getId();
    }
}
