<?php

namespace SwAlgolia\Tests\Unit\Services;

use AlgoliaSearch\Index;

/**
 * Class AlgoliaServiceTest
 * @package SwAlgolia\Tests\Unit\Services
 */
class AlgoliaServiceTest extends BaseTest
{

    /**
     * Set up unit test
     */
    public function setUp()
    {
        parent::setUp();

    }

    /**
     * Test for initialization of the Algolia index
     */
    public function testInitIndex() {

        $algoliaService = Shopware()->Container()->get('sw_algolia.algolia_service');
        $index = $algoliaService->initIndex('swalgolia_1');

        // Do assertion tests
        $this->assertTrue(!$index || $index instanceof Index);

    }

}
