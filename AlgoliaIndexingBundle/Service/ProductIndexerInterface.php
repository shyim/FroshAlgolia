<?php declare(strict_types=1);

namespace FroshAlgolia\AlgoliaIndexingBundle\Service;

use FroshAlgolia\AlgoliaIndexingBundle\Struct\AlgoliaProduct;
use Shopware\Models\Shop\Shop;

interface ProductIndexerInterface
{
    /**
     * @param Shop $shop
     * @param $chunkSize
     * @param $shopConfig
     *
     * @throws \Doctrine\DBAL\DBALException
     *
     * @return array
     */
    public function index(Shop $shop, $chunkSize, array $shopConfig): array;

    /**
     * @param array $numbers
     * @param Shop $shop
     * @param array $shopConfig
     * @return AlgoliaProduct[]
     */
    public function indexNumbers(array $numbers, Shop $shop, array $shopConfig);
}
