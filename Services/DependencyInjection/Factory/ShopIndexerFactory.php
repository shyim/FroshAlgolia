<?php

namespace SwAlgolia\Services\DependencyInjection\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use SwAlgolia\Services\DataIndexerInterface;
use SwAlgolia\Services\MappingInterface;
use SwAlgolia\Services\SettingsInterface;
use SwAlgolia\Services\ShopIndexer;
use SwAlgolia\Services\ShopIndexerInterface;
use Shopware\Components\DependencyInjection\Container;

/**
 * Class ShopIndexerFactory
 * @package SwAlgolia\Services\DependencyInjection\Factory
 */
class ShopIndexerFactory
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var DataIndexerInterface[]
     */
    private $indexer;

    /**
     * @var MappingInterface[]
     */
    private $mappings;

    /**
     * @var SettingsInterface[]
     */
    private $settings;

    /**
     * @param DataIndexerInterface[] $indexer
     * @param MappingInterface[] $mappings
     * @param SettingsInterface[] $settings
     */
    public function __construct(array $indexer, array $mappings, array $settings)
    {
        $this->indexer = $indexer;
        $this->mappings = $mappings;
        $this->settings = $settings;
    }

    /**
     * @param Container $container
     * @return ShopIndexerInterface
     * @throws \Exception
     */
    public function factory(Container $container)
    {
        $this->container = $container;

        $indexer = $this->collectIndexer();
        $mappings = $this->collectMappings();
        $settings = $this->collectSettings();

        return new ShopIndexer(
            $this->container->get('swalgolia.client'),
            $this->container->get('swalgolia.backlog_reader'),
            $this->container->get('swalgolia.backlog_processor'),
            $this->container->get('swalgolia.index_factory'),
            $indexer,
            $mappings,
            $settings,
            $this->container->getParameter('swalgolia')
        );
    }

    /**
     * @return DataIndexerInterface[]
     * @throws \Enlight_Event_Exception
     */
    private function collectIndexer()
    {
        $collection = new ArrayCollection();
        $this->container->get('events')->collect(
            'SwAlgolia_Collect_Indexer',
            $collection
        );

        return array_merge($collection->toArray(), $this->indexer);
    }

    /**
     * @return MappingInterface[]
     * @throws \Enlight_Event_Exception
     */
    private function collectMappings()
    {
        $collection = new ArrayCollection();
        $this->container->get('events')->collect(
            'SwAlgolia_Collect_Mapping',
            $collection
        );

        return array_merge($collection->toArray(), $this->mappings);
    }

    /**
     * @return SettingsInterface[]
     * @throws \Enlight_Event_Exception
     */
    private function collectSettings()
    {
        $collection = new ArrayCollection();
        $this->container->get('events')->collect(
            'SwAlgolia_Collect_Settings',
            $collection
        );
        return array_merge($collection->toArray(), $this->settings);
    }
}
