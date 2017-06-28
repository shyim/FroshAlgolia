<?php

namespace SwAlgolia\Services;

use Doctrine\DBAL\Connection;
use SwAlgolia\Structs\Backlog;

/**
 * Class BacklogProcessorService.
 */
class BacklogProcessor
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(
        Connection $connection
    ) {
        $this->connection = $connection;
    }

    public function add($backlogs)
    {
        if (empty($backlogs)) {
            return;
        }

        $this->writeBacklog($backlogs);
    }

    /**
     * @param Backlog[] $backlogs
     */
    private function writeBacklog(array $backlogs)
    {
        $statement = $this->connection->prepare('
            INSERT IGNORE INTO swalgolia_backlog (`event`, `payload`, `time`)
            VALUES (:event, :payload, :time);
        ');

        foreach ($backlogs as $backlog) {
            $statement->execute([
                ':event' => $backlog->getEvent(),
                ':payload' => json_encode($backlog->getPayload()),
                ':time' => $backlog->getTime()->format('Y-m-d H:i:s'),
            ]);
        }
    }
}
