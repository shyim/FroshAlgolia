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
    private $logger = null;

    /**
     * @var array
     */
    private $pluginConfig = null;

    public function __construct(Components\Logger $logger)
    {

        $this->logger = $logger;

        // Grab the plugin config
        $this->pluginConfig = Shopware()->Container()->get('shopware.plugin.cached_config_reader')->getByPluginName('SwAlgolia');

    }

    /**
     * Build the main index name for a shop
     * @param Shop $shop
     * @return string
     */
    public function buildIndexName(Shop $shop) {

        return $this->pluginConfig['index-prefix-name'] . '_' . $shop->getId();

    }

}