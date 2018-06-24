<?php declare(strict_types=1);

namespace FroshAlgolia\AlgoliaIndexingBundle\ProductProcessor;

use FroshAlgolia\AlgoliaIndexingBundle\Struct\AlgoliaProduct;
use Shopware\Bundle\StoreFrontBundle\Struct\Product;
use Shopware\Models\Media\Media;

class DefaultProcessor implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(Product $product, AlgoliaProduct $algoliaProduct, array $shopConfig): void
    {
        // Get the media
        $media = $product->getMedia();
        $image = null;

        if (!empty($media)) {
            /** @var Media $mediaObject */
            $mediaObject = current($media);
            $image = $mediaObject->getThumbnail(0)->getSource();
        }

        // Get the votes
        $voteAvgPoints = 0;
        $votes = $product->getVoteAverage();
        if ($votes) {
            $voteAvgPoints = (int) $votes->getPointCount()[0]['points'];
        }

        // Build the algolia product
        $algoliaProduct->setObjectID($product->getNumber());
        $algoliaProduct->setArticleId($product->getId());
        $algoliaProduct->setName($product->getName());
        $algoliaProduct->setNumber($product->getNumber());
        $algoliaProduct->setManufacturerName($product->getManufacturer()->getName());
        $algoliaProduct->setPrice(round($product->getCheapestPrice()->getCalculatedPrice(), 2));
        $algoliaProduct->setDescription(strip_tags($product->getShortDescription()));
        $algoliaProduct->setEan($product->getEan());
        $algoliaProduct->setImage($image);
        $algoliaProduct->setCategories($product->getAttribute('categories')->jsonSerialize());
        $algoliaProduct->setAttributes($this->getAttributes($product, $shopConfig));
        $algoliaProduct->setProperties($this->getProperties($product));
        $algoliaProduct->setSales($product->getSales());
        $algoliaProduct->setVotes($votes);
        $algoliaProduct->setVoteAvgPoints($voteAvgPoints);
    }

    /**
     * Get all product attributes.
     *
     * @param Product $product
     * @param array   $shopConfig
     *
     * @return array
     */
    private function getAttributes(Product $product, array $shopConfig)
    {
        $data = [];

        if (!isset($product->getAttributes()['core'])) {
            return [];
        }

        $attributes = $product->getAttributes()['core']->toArray();
        $blockedAttributes = array_column($shopConfig['blockedAttributes'], 'name');

        foreach ($attributes as $key => $value) {
            // Skip this attribute if it´s on the list of blocked attributes
            if (in_array($key, $blockedAttributes) || empty($value)) {
                continue;
            }

            // Map value to data array
            $data[$key] = $value;
        }

        return $data;
    }

    /**
     * Fetches all product properties as an array.
     *
     * @param Product $product
     *
     * @return array
     */
    private function getProperties(Product $product)
    {
        $properties = [];

        if ($set = $product->getPropertySet()) {
            $groups = $set->getGroups();

            foreach ($groups as $group) {
                $options = $group->getOptions();

                foreach ($options as $option) {
                    $properties[$group->getName()] = $option->getName();
                }
            }
        }

        return $properties;
    }
}
