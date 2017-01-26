<?php

namespace SwAlgolia\Services\Product;

use SwAlgolia\Services\LastIdQuery;

/**
 * Interface ProductQueryFactoryInterface.
 */
interface ProductQueryFactoryInterface
{
    /**
     * @param $categoryId
     * @param null|int $limit
     *
     * @return LastIdQuery
     */
    public function createCategoryQuery($categoryId, $limit = null);

    /**
     * @param int[]    $priceIds
     * @param int|null $limit
     *
     * @return LastIdQuery
     */
    public function createPriceIdQuery($priceIds, $limit = null);

    /**
     * @param int[]    $unitIds
     * @param int|null $limit
     *
     * @return LastIdQuery
     */
    public function createUnitIdQuery($unitIds, $limit = null);

    /**
     * @param int[]    $voteIds
     * @param int|null $limit
     *
     * @return LastIdQuery
     */
    public function createVoteIdQuery($voteIds, $limit = null);

    /**
     * @param int[]    $productIds
     * @param int|null $limit
     *
     * @return LastIdQuery
     */
    public function createProductIdQuery($productIds, $limit = null);

    /**
     * @param int[]    $variantIds
     * @param int|null $limit
     *
     * @return LastIdQuery
     */
    public function createVariantIdQuery($variantIds, $limit = null);

    /**
     * @param int[]    $taxIds
     * @param int|null $limit
     *
     * @return LastIdQuery
     */
    public function createTaxQuery($taxIds, $limit = null);

    /**
     * @param int[]    $manufacturerIds
     * @param int|null $limit
     *
     * @return LastIdQuery
     */
    public function createManufacturerQuery($manufacturerIds, $limit = null);

    /**
     * @param int[]    $categoryIds
     * @param int|null $limit
     *
     * @return LastIdQuery
     */
    public function createProductCategoryQuery($categoryIds, $limit = null);

    /**
     * @param int[]    $groupIds
     * @param int|null $limit
     *
     * @return LastIdQuery
     */
    public function createPropertyGroupQuery($groupIds, $limit = null);

    /**
     * @param int[]    $optionIds
     * @param int|null $limit
     *
     * @return LastIdQuery
     */
    public function createPropertyOptionQuery($optionIds, $limit = null);
}
