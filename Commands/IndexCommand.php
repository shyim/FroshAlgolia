<?php

namespace SwAlgolia\Commands;

use Shopware\Commands\ShopwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Shopware\Components;
use Shopware\Components\Api\Resource\Resource;

class IndexCommand extends ShopwareCommand
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
            ->setName('algoliaindex')
            ->setDescription('Used to perform different operations on the Algolia index.')
            ->addArgument(
                'action',
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
        $action = $input->getArgument('action');

        $indexService = $this->getContainer()->get('sw_algolia.algolia_index_service');
        $indexService->push();

    }

}
