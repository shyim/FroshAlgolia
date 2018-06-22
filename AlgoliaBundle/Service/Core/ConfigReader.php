<?php declare(strict_types=1);

namespace FroshAlgolia\AlgoliaBundle\Service\Core;

use FroshAlgolia\AlgoliaBundle\Service\ConfigReaderInterface;
use FroshAlgolia\Models\Config;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Shop\Shop;

/**
 * Class ConfigReader.
 */
class ConfigReader implements ConfigReaderInterface
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
        $this->defaultConfig = include $pluginDir . '/Bootstrap/DefaultConfig.php';
    }

    /**
     * {@inheritdoc}
     */
    public function read(Shop $shop): array
    {
        $customConfig = $this->models->getRepository(Config::class)->findOneBy(['shop' => $shop->getId()]);

        $data = array_merge($this->defaultConfig, $customConfig ? $customConfig->getConfig() : []);

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToAlgoliaRanking(array $value): array
    {
        $algoliaSchema = [];

        foreach ($value as $item) {
            $algoliaSchema[] = sprintf('%s(%s)', $item['sort'], $item['name']);
        }

        return $algoliaSchema;
    }
}
