<?php

namespace FroshAlgolia\Components\Service;

use Shopware\Bundle\StoreFrontBundle\Struct\ListProduct;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;

interface CategoryServiceInterface
{
    /**
     * @param ListProduct[]        $products
     * @param ShopContextInterface $shopContext
     *
     * @return array
     */
    public function getCategories(array $products, ShopContextInterface $shopContext);

    /**
     * @param array                $path
     * @param ShopContextInterface $context
     */
    public function buildHierarchicalWithPath(array $path, ShopContextInterface $context);

    /**
     * @param array                $path
     * @param ShopContextInterface $context
     *
     * @return string
     */
    public function buildPath(array $path, ShopContextInterface $context);
}