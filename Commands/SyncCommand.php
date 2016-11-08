<?php

namespace SwAlgolia\Commands;

use Shopware\Commands\ShopwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Shopware\Components;

class SyncCommand extends ShopwareCommand
{

    private $logger = null;

    /**
     * DiscountPriceCalculationService constructor.
     * @param Components\Logger $logger
     */
    public function __construct(Components\Logger $logger) {

        $this->logger = $logger;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('algoliasync')
            ->setDescription('Used to perform operations on the Algolia index.')
            ->addArgument(
                'operation',
                InputArgument::REQUIRED,
                'The action that should be performed on the Algolia index.'
            )
            ->setHelp(<<<EOF
The <info>%command.name%</info> is responsible to execute different operations on the Algolia index.
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
        $syncService = $this->getContainer()->get('sw_algolia.sync_service');

        switch($operation):

            case 'full':
                $syncService->fullSync();

        endswitch;

    }

}
