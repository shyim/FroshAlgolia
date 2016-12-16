<?php

namespace SwAlgolia\Services;

use Doctrine\DBAL\Connection;
use SwAlgolia\Structs\Backlog;
use SwAlgolia\Structs\ShopIndex;

/**
 * Class BacklogProcessorService
 * @package SwAlgolia\Services
 */
class BacklogProcessor implements BacklogProcessorInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var SynchronizerInterface
     */
    private $synchronizer;

    /**
     * @var IndexFactoryInterface
     */
    private $indexFactory;

    /**
     * @var IdentifierSelector
     */
    private $identifierSelector;

    /**
     * @param Connection $connection
     * @param SynchronizerInterface $synchronizer
     * @param IndexFactoryInterface $indexFactory
     * @param IdentifierSelector $identifierSelector
     */
    public function __construct(
        Connection $connection,
        SynchronizerInterface $synchronizer,
        IndexFactoryInterface $indexFactory,
        IdentifierSelector $identifierSelector
    ) {
        $this->connection = $connection;
        $this->synchronizer = $synchronizer;
        $this->indexFactory = $indexFactory;
        $this->identifierSelector = $identifierSelector;
    }

    /**
     * {@inheritdoc}
     */
    public function add($backlogs)
    {
        if (empty($backlogs)) {
            return;
        }

        $this->writeBacklog($backlogs);
    }

    /**
     * {@inheritdoc}
     */
    public function process(ShopIndex $shopIndex, $backlogs)
    {
        $this->synchronizer->synchronize($shopIndex, $backlogs);
    }

    /**
     * @param Backlog[] $backlogs
     */
    private function writeBacklog(array $backlogs)
    {
        $statement = $this->connection->prepare("
            INSERT IGNORE INTO swalgolia_backlog (`event`, `payload`, `time`)
            VALUES (:event, :payload, :time);
        ");

        foreach ($backlogs as $backlog) {
            $statement->execute([
                ':event'   => $backlog->getEvent(),
                ':payload' => json_encode($backlog->getPayload()),
                ':time'    => $backlog->getTime()->format('Y-m-d H:i:s')
            ]);
        }
    }
}
