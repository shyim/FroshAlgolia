<?php

namespace SwAlgolia\Commands;

use Shopware\Commands\ShopwareCommand;
use SwAlgolia\Services\SyncService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Shopware\Components;

class SyncCommand extends ShopwareCommand
{
    /**
     * @var Components\Logger
     */
    private $logger;

    /**
     * @var SyncService
     */
    private $syncService;

    /**
     * DiscountPriceCalculationService constructor.
     * @param Components\Logger $logger
     * @param SyncService       $syncService
     */
    public function __construct(
        Components\Logger $logger,
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
            ->setName('swalgoliasync')
            ->setDescription('Used to perform operations on the Algolia index.')
            ->addArgument(
                'operation',
                InputArgument::REQUIRED,
                'The action that should be performed on the Algolia index.'
            )
            ->setHelp(<<<EOF
Allowed arguments full,fullsync
EOF
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Fetch input data
        $operation = $input->getArgument('operation');

        switch ($operation):
            case 'fullsync':
            case 'full':
                $this->syncService->fullSync();

        endswitch;
    }
}
