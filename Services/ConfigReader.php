<?php

namespace FroshAlgolia\Services;

use Shopware\Components\Model\ModelManager;
use Shopware\Models\Shop\Shop;
use FroshAlgolia\Models\Config;

/**
 * Class ConfigReader.
 */
class ConfigReader
{
    /**
     * @var ModelManager
     */
    private $models;

    /**
     * @var array
     */
    private $defaultConfig;

    /**
     * ConfigReader constructor.
     *
     * @param ModelManager $modelManager
     * @param string       $pluginDir
     */
    public function __construct(
        ModelManager $modelManager,
        $pluginDir
    ) {
        $this->models = $modelManager;
        $this->defaultConfig = include $pluginDir.'/Bootstrap/DefaultConfig.php';
    }

    /**
     * @param Shop $shop
     *
     * @return array
     */
    public function read(Shop $shop)
    {
        $customConfig = $this->models->getRepository(Config::class)->findOneBy(['shop' => $shop->getId()]);

        $data = array_merge($this->defaultConfig, $customConfig ? $customConfig->getConfig() : []);
        $data['facetFilterWidgetArray'] = json_decode($data['facetFilterWidget'], true);

        return $data;
    }

    /**
     * Converts a array of name and sort to algolia schema.
     *
     * @param array $value
     *
     * @return array
     */
    public function convertToAlgoliaRanking(array $value)
    {
        $algoliaSchema = [];

        foreach ($value as $item) {
            $algoliaSchema[] = sprintf('%s(%s)', $item['sort'], $item['name']);
        }

        return $algoliaSchema;
    }
}
