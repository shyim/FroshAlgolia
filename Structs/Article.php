<?php

namespace SwAlgolia\Structs;

use Shopware\Bundle\StoreFrontBundle\Struct\Product\VoteAverage;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Article
 */
class Article extends Struct implements StructInterface
{
    /**
     * @var null|int
     *
     * @Assert\NotBlank()
     */
    public $objectID;

    /**
     * @var null|string
     *
     * @Assert\NotBlank()
     */
    public $name;

    /**
     * @var integer
     *
     * @Assert\NotBlank()
     */
    public $articleId;

    /**
     * @var null|string
     *
     * @Assert\NotBlank()
     */
    private $number;

    /**
     * @var null|string
     */
    private $manufacturer_name;

    /**
     * @var null|string
     */
    private $currencySymbol;

    /**
     * @var null|float
     */
    private $price;

    /**
     * @var null|float
     */
    private $link;

    /**
     * @var null|string
     */
    private $description;

    /**
     * @var null|string
     */
    private $ean;

    /**
     * @var null|string
     */
    private $image;

    /**
     * @var null|array
     */
    private $categories;

    /**
     * @var null|array
     */
    private $attributes;

    /**
     * @var null|array
     */
    private $properties;

    /**
     * @var int
     */
    private $sales = 0;

    /**
     * @var null|VoteAverage
     */
    private $votes;

    /**
     * @var int|null
     */
    private $voteAvgPoints;

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
     * @return integer
     */
    public function getArticleId()
    {
        return $this->articleId;
    }

    /**
     * @param integer $articleId
     */
    public function setArticleId($articleId)
    {
        $this->articleId = $articleId;
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
     * @return null|string
     */
    public function getCurrencySymbol()
    {
        return $this->currencySymbol;
    }

    /**
     * @param null|string $currencySymbol
     */
    public function setCurrencySymbol($currencySymbol)
    {
        $this->currencySymbol = $currencySymbol;
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

    /**
     * @return VoteAverage|null
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * @param VoteAverage|null $votes
     */
    public function setVotes($votes)
    {
        $this->votes = $votes;
    }

    /**
     * @return mixed
     */
    public function getVoteAvgPoints()
    {
        return $this->voteAvgPoints;
    }

    /**
     * @param int $voteAvgPoints
     */
    public function setVoteAvgPoints($voteAvgPoints)
    {
        $this->voteAvgPoints = $voteAvgPoints;
    }
}
