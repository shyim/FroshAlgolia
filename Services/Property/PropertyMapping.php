<?php

namespace SwAlgolia\Services\Property;

use SwAlgolia\Services\FieldMappingInterface;
use SwAlgolia\Services\MappingInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\Shop;

/**
 * Class PropertyMapping.
 */
class PropertyMapping implements MappingInterface
{
    const TYPE = 'property';

    /**
     * @var FieldMappingInterface
     */
    private $fieldMapping;

    /**
     * @param FieldMappingInterface $fieldMapping
     */
    public function __construct(FieldMappingInterface $fieldMapping)
    {
        $this->fieldMapping = $fieldMapping;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function get(Shop $shop)
    {
        return [
            'properties' => [
                'id' => ['type' => 'long'],
                'name' => $this->fieldMapping->getLanguageField($shop),
                'filterable' => ['type' => 'boolean'],
                'options' => [
                    'properties' => [
                        'id' => ['type' => 'long'],
                        'name' => $this->fieldMapping->getLanguageField($shop),
                    ],
                ],
            ],
        ];
    }
}
