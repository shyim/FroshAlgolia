<?php

namespace SwAlgolia\Tests\Unit\Services;

use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Shop\Shop;

/**
 * Class AlgoliaServiceTest
 * @package SwAlgolia\Tests\Unit\Services
 */
class BaseTest extends TestCase
{

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Shop
     */
    private $shop;

    /**
     * Set up unit test
     */
    public function setUp()
    {
        parent::setUp();
        $this->em = Shopware()->Models();
        $repository = Shopware()->Container()->get('models')->getRepository('Shopware\Models\Shop\Shop');
        $this->shop = $repository->getActiveById(1);
    }

    public function testBase()
    {
        $this->assertInstanceOf('Doctrine\ORM\EntityManager', $this->em);
        $this->assertInstanceOf('Shopware\Models\Shop\Shop', $this->shop);
    }
}
