<?php

namespace SwAlgolia\Tests\Unit\Services;

use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Shop\Shop;

/**
 * Class AlgoliaServiceTest.
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
     * Set up unit test.
     */
    public function setUp()
    {
        parent::setUp();
        $this->em = Shopware()->Models();
        $repository = Shopware()->Container()->get('models')->getRepository(Shop::class);
        $this->shop = $repository->getActiveById(1);
    }

    public function testBase()
    {
        $this->assertInstanceOf(EntityManager::class, $this->em);
        $this->assertInstanceOf(Shop::class, $this->shop);
    }
}
