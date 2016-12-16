<?php

namespace SwAlgolia\Structs;

/**
 * Class IndexConfiguration
 * @package SwAlgolia\Structs
 */
class IndexConfiguration
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $alias;

    /**
     * @var int|null
     */
    private $numberOfShards = null;

    /**
     * @var int|null
     */
    private $numberOfReplicas = null;

    /**
     * @param string $name
     * @param string $alias
     * @param int|null $numberOfShards
     * @param int|null $numberOfReplicas
     */
    public function __construct($name, $alias, $numberOfShards = null, $numberOfReplicas = null)
    {
        $this->name = $name;
        $this->alias = $alias;
        $this->numberOfShards = $numberOfShards;
        $this->numberOfReplicas = $numberOfReplicas;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @return int|null
     */
    public function getNumberOfShards()
    {
        return $this->numberOfShards;
    }

    /**
     * @param int|null $numberOfShards
     */
    public function setNumberOfShards($numberOfShards)
    {
        $this->numberOfShards = $numberOfShards;
    }

    /**
     * @return int|null
     */
    public function getNumberOfReplicas()
    {
        return $this->numberOfReplicas;
    }

    /**
     * @param int|null $numberOfReplicas
     */
    public function setNumberOfReplicas($numberOfReplicas)
    {
        $this->numberOfReplicas = $numberOfReplicas;
    }
}
