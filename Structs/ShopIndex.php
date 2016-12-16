<?php

namespace SwAlgolia\Structs;

use Shopware\Bundle\StoreFrontBundle\Struct\Shop;

/**
 * Class ShopIndex
 * @package SwAlgolia\Structs
 */
class ShopIndex
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Shop $shop
     */
    private $shop;

    /**
     * @param string $name
     * @param Shop $shop
     */
    public function __construct($name, Shop $shop)
    {
        $this->name = $name;
        $this->shop = $shop;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Shop
     */
    public function getShop()
    {
        return $this->shop;
    }
}
