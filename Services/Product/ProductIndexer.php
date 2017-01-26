<?php

namespace SwAlgolia\Services\Product;

use Elasticsearch\Client;
use SwAlgolia\Console\ProgressHelperInterface;
use SwAlgolia\Services\DataIndexerInterface;
use SwAlgolia\Structs\ShopIndex;

class ProductIndexer implements DataIndexerInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var ProductProviderInterface
     */
    private $provider;

    /**
     * @var ProductQueryFactoryInterface
     */
    private $queryFactory;

    /**
     * @param Client                       $client
     * @param ProductProviderInterface     $provider
     * @param ProductQueryFactoryInterface $queryFactory
     */
    public function __construct(
        Client $client,
        ProductProviderInterface $provider,
        ProductQueryFactoryInterface $queryFactory
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
        $categoryId = $index->getShop()->getCategory()->getId();
        $idQuery = $this->queryFactory->createCategoryQuery($categoryId, 100);
        $progress->start($idQuery->fetchCount(), 'Indexing products');

        while ($ids = $idQuery->fetch()) {
            $query = $this->queryFactory->createProductIdQuery($ids);
            $this->indexProducts($index, $query->fetch());
            $progress->advance(count(array_unique($ids)));
        }

        $progress->finish();
    }

    /**
     * @param ShopIndex $index
     * @param $numbers
     */
    public function indexProducts(ShopIndex $index, $numbers)
    {
        if (empty($numbers)) {
            return null;
        }

        $products = $this->provider->get($index->getShop(), $numbers);
        $remove = array_diff($numbers, array_keys($products));

        $documents = [];
        foreach ($products as $product) {
            $documents[] = ['index' => ['_id' => $product->getNumber()]];
            $documents[] = $product;
        }

        foreach ($remove as $number) {
            $documents[] = ['delete' => ['_id' => $number]];
        }

        $this->client->bulk([
            'index' => $index->getName(),
            'type' => ProductMapping::TYPE,
            'body' => $documents,
        ]);

        return null;
    }
}
