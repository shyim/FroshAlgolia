<?php

namespace SwAlgolia\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="swalgolia_config")
 */
class Config
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(name="shop", type="integer", nullable=false)
     */
    private $shop = null;

    /**
     * @ORM\Column(name="config", type="json_array", nullable=false)
     */
    private $config = null;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Config
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getShop()
    {
        return $this->shop;
    }

    /**
     * @param int $shop
     * @return Config
     */
    public function setShop($shop)
    {
        $this->shop = $shop;
        return $this;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     * @return Config
     */
    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }
}
