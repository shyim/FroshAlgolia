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
     * Test for initialization of the Algolia index
     */
    public function testInitIndex()
    {
        $algoliaService = Shopware()->Container()->get('sw_algolia.algolia_service');
        $index = $algoliaService->initIndex('swalgolia_1');

        // Do assertion tests
        $this->assertTrue(!$index || $index instanceof Index);
    }
}
