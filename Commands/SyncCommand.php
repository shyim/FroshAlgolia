<?php declare(strict_types=1);

namespace FroshAlgolia\Commands;

use FroshAlgolia\Services\SyncService;
use Shopware\Commands\ShopwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SyncCommand.
 */
class SyncCommand extends ShopwareCommand
{
    /**
     * @var SyncService
     */
    private $syncService;

    /**
     * @param SyncService $syncService
     */
    public function __construct(
        SyncService $syncService
    ) {
        $this->syncService = $syncService;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('algolia:sync')
            ->setDescription('Used to perform operations on the Algolia index.');
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->syncService->fullSync();
    }
}
