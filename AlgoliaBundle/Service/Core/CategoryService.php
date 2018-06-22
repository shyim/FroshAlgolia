<?php declare(strict_types=1);

namespace FroshAlgolia\AlgoliaBundle\Service\Core;

use Doctrine\DBAL\Connection;
use Shopware\Bundle\StoreFrontBundle\Service\CategoryServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\Category;
use Shopware\Bundle\StoreFrontBundle\Struct\ListProduct;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;

class CategoryService implements \FroshAlgolia\AlgoliaBundle\Service\CategoryServiceInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var CategoryServiceInterface
     */
    private $storeFrontCategoryService;

    /**
     * CategoryService constructor.
     *
     * @param Connection               $connection
     * @param CategoryServiceInterface $storeFrontCategoryService
     */
    public function __construct(Connection $connection, CategoryServiceInterface $storeFrontCategoryService)
    {
        $this->connection = $connection;
        $this->storeFrontCategoryService = $storeFrontCategoryService;
    }

    /**
     * {@inheritdoc}
     */
    public function getCategories(array $products, ShopContextInterface $shopContext)
    {
        $productIds = array_map(function (ListProduct $product) {
            return $product->getId();
        }, $products);

        $qb = $this->connection->createQueryBuilder();
        $qb->from('s_articles_categories', 'categories')
            ->select('categories.articleID, categories.categoryID')
            ->andWhere('categories.articleID IN (:ids)')
            ->setParameter('ids', $productIds, Connection::PARAM_INT_ARRAY);

        $categories = $qb->execute()->fetchAll(\PDO::FETCH_GROUP | \PDO::FETCH_COLUMN);

        $result = [];

        foreach ($categories as $productId => $productCategories) {
            foreach ($products as $product) {
                if ($product->getId() == $productId) {
                    foreach ($productCategories as $productCategory) {
                        $category = $this->getProductCategory($product, $productCategory);

                        if ($this->isValidCategory($category, $shopContext)) {
                            if (!isset($result[$productId])) {
                                $result[$productId] = [];
                            }

                            $result[$productId] = $this->mergeTree($result[$productId], $this->buildCategoryHierarchical($product, $category));
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function buildHierarchicalWithPath(array $path, ShopContextInterface $context)
    {
        $categorys = $this->storeFrontCategoryService->getList($path, $context);

        $max = count($path);

        for ($i = 1; $i <= $max; ++$i) {
            $items = array_slice($path, 0, $i);

            $items = array_map(function ($id) use ($categorys) {
                return $categorys[$id]->getName();
            }, $items);

            $result['lvl' . ($i - 1)] = [implode(' > ', $items)];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildPath(array $path, ShopContextInterface $context): string
    {
        $categorys = $this->storeFrontCategoryService->getList($path, $context);

        $items = array_map(function ($id) use ($categorys) {
            return $categorys[$id]->getName();
        }, $path);

        return implode(' > ', $items);
    }

    /**
     * @param ListProduct $product
     * @param $categoryId
     *
     * @return null|Category
     */
    private function getProductCategory(ListProduct $product, $categoryId): ?Category
    {
        foreach ($product->getCategories() as $category) {
            if ($category->getId() == $categoryId) {
                return $category;
            }
        }

        return null;
    }

    /**
     * @param Category             $category
     * @param ShopContextInterface $shopContext
     *
     * @return bool
     */
    private function isValidCategory(Category $category, ShopContextInterface $shopContext)
    {
        if (\in_array($shopContext->getCurrentCustomerGroup()->getId(), $category->getBlockedCustomerGroupIds())) {
            return false;
        }

        if (\in_array($shopContext->getFallbackCustomerGroup()->getId(), $category->getBlockedCustomerGroupIds())) {
            return false;
        }

        if (!\in_array($shopContext->getShop()->getCategory()->getId(), $category->getPath())) {
            return false;
        }

        return true;
    }

    /**
     * @param ListProduct $product
     * @param Category    $category
     *
     * @return array
     */
    private function buildCategoryHierarchical($product, $category)
    {
        $result = [];
        $path = $category->getPath();
        $path[] = $category->getId();
        $path = array_slice($path, 1);
        $max = count($path);

        for ($i = 1; $i <= $max; ++$i) {
            $items = array_slice($path, 0, $i);

            $me = $this;

            $items = array_map(function ($id) use ($product, $me) {
                return $me->getProductCategory($product, $id)->getName();
            }, $items);

            $result['lvl' . ($i - 1)] = [implode(' > ', $items)];
        }

        return $result;
    }

    private function mergeTree(array $tree1, array $tree2)
    {
        foreach ($tree2 as $key => $values) {
            if (!isset($tree1[$key])) {
                $tree1[$key] = $tree2[$key];
            }

            if (!in_array($values[0], $tree1[$key])) {
                $tree1[$key][] = $values[0];
            }
        }

        return $tree1;
    }
}
