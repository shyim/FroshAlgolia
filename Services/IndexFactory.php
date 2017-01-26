<?php

namespace SwAlgolia\Services;

use SwAlgolia\Structs\IndexConfiguration;
use SwAlgolia\Structs\ShopIndex;
use Shopware\Bundle\StoreFrontBundle\Struct\Shop;

/**
 * Class IndexFactory.
 */
class IndexFactory implements IndexFactoryInterface
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @var int|null
     */
    private $numberOfShards;

    /**
     * @var int|null
     */
    private $numberOfReplicas;

    /**
     * @param string   $prefix
     * @param int|null $numberOfShards
     * @param int|null $numberOfReplicas
     */
    public function __construct($prefix, $numberOfShards = null, $numberOfReplicas = null)
    {
        $this->prefix = $prefix;
        $this->numberOfShards = $numberOfShards;
        $this->numberOfReplicas = $numberOfReplicas;
    }

    /**
     * @param Shop $shop
     *
     * @return IndexConfiguration
     */
    public function createIndexConfiguration(Shop $shop)
    {
        return new IndexConfiguration(
            $this->getIndexName($shop).'_'.$this->getTimestamp(),
            $this->getIndexName($shop),
            $this->numberOfShards,
            $this->numberOfReplicas
        );
    }

    /**
     * @param Shop $shop
     *
     * @return ShopIndex
     */
    public function createShopIndex(Shop $shop)
    {
        return new ShopIndex($this->getIndexName($shop), $shop);
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @return string
     */
    private function getTimestamp()
    {
        $date = new \DateTime();

        return $date->format('YmdHis');
    }

    /**
     * @param Shop $shop
     *
     * @return string
     */
    private function getIndexName(Shop $shop)
    {
        return $this->getPrefix().$shop->getId();
    }
}
