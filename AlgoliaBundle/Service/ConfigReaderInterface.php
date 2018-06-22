<?php declare(strict_types=1);

namespace FroshAlgolia\AlgoliaBundle\Service;

use Shopware\Models\Shop\Shop;

interface ConfigReaderInterface
{
    /**
     * @param Shop $shop
     *
     * @return array
     */
    public function read(Shop $shop): array;

    /**
     * Converts a array of name and sort to algolia schema.
     *
     * @param array $value
     *
     * @return array
     */
    public function convertToAlgoliaRanking(array $value): array;
}
