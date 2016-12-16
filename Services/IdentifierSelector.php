<?php

namespace SwAlgolia\Services;

use Doctrine\DBAL\Connection;
use Shopware\Bundle\StoreFrontBundle\Gateway\ShopGatewayInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\Shop;

/**
 * Class IdentifierSelector
 * @package SwAlgolia\Services
 */
class IdentifierSelector
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var ShopGatewayInterface
     */
    private $shopGateway;

    /**
     * @param Connection $connection
     * @param ShopGatewayInterface $shopGateway
     */
    public function __construct(
        Connection $connection,
        ShopGatewayInterface $shopGateway
    ) {
        $this->connection = $connection;
        $this->shopGateway = $shopGateway;
    }

    /**
     * @return Shop[]
     */
    public function getShops()
    {
        return $this->shopGateway->getList($this->getShopIds());
    }

    /**
     * @return int[]
     */
    public function getShopIds()
    {
        return $this->connection->createQueryBuilder()
            ->select('id')
            ->from('s_core_shops', 'shop')
            ->execute()
            ->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * @return string[]
     */
    public function getCustomerGroupKeys()
    {
        return $this->connection->createQueryBuilder()
            ->select('groupkey')
            ->from('s_core_customergroups', 'customerGroups')
            ->execute()
            ->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * @param int $shopId
     * @return int[]
     */
    public function getShopCurrencyIds($shopId)
    {
        return $this->connection->createQueryBuilder()
            ->select('currency_id')
            ->from('s_core_shop_currencies', 'currency')
            ->andWhere('currency.shop_id = :id')
            ->setParameter(':id', $shopId)
            ->execute()
            ->fetchAll(\PDO::FETCH_COLUMN);
    }
}
