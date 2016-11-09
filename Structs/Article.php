<?php

namespace SwAlgolia\Structs;

use Symfony\Component\Validator\Constraints as Assert;

class Article extends Struct implements StructInterface
{

    /**
     * @var null|int
     * @Assert\NotBlank()
     */
    public $objectID = null;

    /**
     * @var null|string
     * @Assert\NotBlank()
     */
    public $name = null;

    /**
     * @var null|string
     * @Assert\NotBlank()
     */
    private $number = null;

    /**
     * @var null|string
     */
    private $manufacturer_name = null;

    /**
     * @var null|float
     */
    private $price = null;

    /**
     * @var null|float
     */
    private $link = null;

    /**
     * @var null|string
     */
    private $description = null;

    /**
     * @var null|string
     */
    private $ean = null;

    /**
     * @var null|string
     */
    private $image = null;

    /**
     * @var null|array
     */
    private $categories = null;

    /**
     * @var null|array
     */
    private $categoryIds = null;

    /**
     * @var null|array
     */
    private $attributes = null;

    /**
     * @var null|array
     */
    private $properties = null;

    /**
     * @var int
     */
    private $sales = 0;

    /**
     * @return int|null
     */
    public function getObjectID()
    {
        return $this->objectID;
    }

    /**
     * @param int|null $objectID
     */
    public function setObjectID($objectID)
    {
        $this->objectID = $objectID;
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return null|string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param null|string $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @return null|string
     */
    public function getManufacturerName()
    {
        return $this->manufacturer_name;
    }

    /**
     * @param null|string $manufacturer_name
     */
    public function setManufacturerName($manufacturer_name)
    {
        $this->manufacturer_name = $manufacturer_name;
    }

    /**
     * @return float|null
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float|null $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return float|null
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param float|null $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * @return null|string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param null|string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return null|string
     */
    public function getEan()
    {
        return $this->ean;
    }

    /**
     * @param null|string $ean
     */
    public function setEan($ean)
    {
        $this->ean = $ean;
    }

    /**
     * @return null|string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param null|string $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return array|null
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param array|null $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    /**
     * @return array|null
     */
    public function getCategoryIds()
    {
        return $this->categoryIds;
    }

    /**
     * @param array|null $categoryIds
     */
    public function setCategoryIds($categoryIds)
    {
        $this->categoryIds = $categoryIds;
    }

    /**
     * @return array|null
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array|null $attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @return array|null
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param array|null $properties
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;
    }


    /**
     * @return int
     */
    public function getSales()
    {
        return $this->sales;
    }

    /**
     * @param int $sales
     */
    public function setSales($sales)
    {
        $this->sales = $sales;
    }

}