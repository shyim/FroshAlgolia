<?php

namespace SwAlgolia\Commands;

use Shopware\Commands\ShopwareCommand;
use Shopware\Components\Logger;
use SwAlgolia\Services\SyncService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SyncCommand.
 */
class SyncCommand extends ShopwareCommand
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var SyncService
     */
    private $syncService;

    /**
     * DiscountPriceCalculationService constructor.
     *
     * @param Logger      $logger
     * @param SyncService $syncService
     */
    public function __construct(
        Logger $logger,
        SyncService $syncService
    ) {
        $this->logger = $logger;
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
            ->setDescription('Used to perform operations on the Algolia index.')
            ->addArgument(
                'operation',
                InputArgument::REQUIRED,
                'The action that should be performed on the Algolia index.'
            )
            ->setHelp(<<<'EOF'
Allowed arguments full,fullsync
EOF
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Fetch input data
        $operation = $input->getArgument('operation');

        switch ($operation) {
            case 'fullsync':
            case 'full':
                $this->syncService->fullSync();
        }
    }
}
