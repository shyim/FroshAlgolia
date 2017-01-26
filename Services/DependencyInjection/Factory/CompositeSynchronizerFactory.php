<?php

namespace SwAlgolia\Services\DependencyInjection\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use SwAlgolia\Services\CompositeSynchronizerService;
use SwAlgolia\Services\SynchronizerInterface;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class CompositeSynchronizerFactory.
 */
class CompositeSynchronizerFactory
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var SynchronizerInterface[]
     */
    private $synchronizer;

    /**
     * @param SynchronizerInterface[] $synchronizer
     */
    public function __construct($synchronizer)
    {
        $this->synchronizer = $synchronizer;
    }

    /**
     * @param Container $container
     *
     * @return CompositeSynchronizerService
     */
    public function factory(Container $container)
    {
        $this->container = $container;
        $synchronizer = $this->collectSynchronizer();

        return new CompositeSynchronizerService($synchronizer);
    }

    /**
     * @return SynchronizerInterface[]
     */
    private function collectSynchronizer()
    {
        $collection = new ArrayCollection();
        $this->container->get('events')->collect(
            'SwAlgolia_Collect_Synchronizer',
            $collection
        );

        return array_merge($collection->toArray(), $this->synchronizer);
    }
}
