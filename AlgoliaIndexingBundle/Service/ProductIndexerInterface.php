<?php

namespace FroshAlgolia\AlgoliaIndexingBundle\Service;

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
}