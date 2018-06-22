<?php declare(strict_types=1);

namespace FroshAlgolia\AlgoliaBundle\Service;

use Shopware\Models\Shop\Shop;

interface IndexNameBuilderInterface
{
    /**
     * @param Shop $shop
     *
     * @return string
     */
    public function buildName(Shop $shop): string;
}
