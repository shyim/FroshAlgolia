<?php

namespace FroshAlgolia\AlgoliaIndexingBundle\Commands;


use Shopware\Commands\ShopwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ClearBacklogCommand extends ShopwareCommand
{
    public function configure()
    {
        $this->setName('algolia:backlog:clear')
            ->setDescription('Clears the algolia backlog');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->container->get('dbal_connection')
            ->executeQuery('TRUNCATE FroshAlgolia_backlog');

        $io = new SymfonyStyle($input, $output);
        $io->success('Cleared backlog!');
    }
}