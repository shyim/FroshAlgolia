<?php declare(strict_types=1);

namespace FroshAlgolia\Services;

use Doctrine\DBAL\Connection;
use FroshAlgolia\Structs\Backlog;

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

    /**
     * @param $backlogs
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function add($backlogs)
    {
        if (empty($backlogs)) {
            return;
        }

        $this->writeBacklog($backlogs);
    }

    /**
     * @param Backlog[] $backlogs
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function writeBacklog(array $backlogs)
    {
        $statement = $this->connection->prepare('
            INSERT IGNORE INTO FroshAlgolia_backlog (`event`, `payload`, `time`)
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
