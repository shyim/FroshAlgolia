<?php

namespace FroshAlgolia\AlgoliaBundle\Service\Core;

use FroshAlgolia\AlgoliaBundle\Service\IndexNameBuilderInterface;
use Shopware\Models\Shop\Shop;

class IndexNameBuilder implements IndexNameBuilderInterface
{
    /**
     * @var array
     */
    private $pluginConfig;

    /**
     * IndexNameBuilder constructor.
     * @param array $pluginConfig
     */
    public function __construct(array $pluginConfig)
    {
        $this->pluginConfig = $pluginConfig;
    }

    /**
     * @param Shop $shop
     * @return string
     */
    public function buildName(Shop $shop): string
    {
        $prefix = isset($this->pluginConfig['index-prefix-name']) && $this->pluginConfig['index-prefix-name'] != '' ? $this->pluginConfig['index-prefix-name'] . '_' : false;

        return $prefix . $shop->getId();
    }
}