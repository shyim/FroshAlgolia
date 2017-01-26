<?php

namespace SwAlgolia\Services\Property;

use AlgoliaSearch\Client;
use SwAlgolia\Console\ProgressHelperInterface;
use SwAlgolia\Services\DataIndexerInterface;
use SwAlgolia\Structs\ShopIndex;
use Shopware\Bundle\StoreFrontBundle\Struct\Property\Group;

/**
 * Class PropertyIndexer.
 */
class PropertyIndexer implements DataIndexerInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var PropertyProvider
     */
    private $provider;

    /**
     * @var PropertyQueryFactory
     */
    private $queryFactory;

    /**
     * @param Client               $client
     * @param PropertyQueryFactory $queryFactory
     * @param PropertyProvider     $provider
     */
    public function __construct(
        Client $client,
        PropertyQueryFactory $queryFactory,
        PropertyProvider $provider
    ) {
        $this->client = $client;
        $this->provider = $provider;
        $this->queryFactory = $queryFactory;
    }

    /**
     * @param ShopIndex               $index
     * @param ProgressHelperInterface $progress
     */
    public function populate(ShopIndex $index, ProgressHelperInterface $progress)
    {
        $query = $this->queryFactory->createQuery();
        $progress->start($query->fetchCount(), 'Indexing properties');

        while ($ids = $query->fetch()) {
            $this->indexProperties($index, $ids);
            $progress->advance(count($ids));
        }

        $progress->finish();
    }

    /**
     * @param ShopIndex $index
     * @param int[]     $groupIds
     */
    public function indexProperties(ShopIndex $index, $groupIds)
    {
        if (empty($groupIds)) {
            return;
        }

        /** @var Group[] $properties */
        $properties = $this->provider->get($index->getShop(), $groupIds);
        $remove = array_diff($groupIds, array_keys($properties));

        $documents = [];
        foreach ($properties as $property) {
            $documents[] = ['index' => ['_id' => $property->getId()]];
            $documents[] = $property;
        }

        foreach ($remove as $id) {
            $documents[] = ['delete' => ['_id' => $id]];
        }

        $this->client->bulk([
            'index' => $index->getName(),
            'type' => PropertyMapping::TYPE,
            'body' => $documents,
        ]);
    }
}
