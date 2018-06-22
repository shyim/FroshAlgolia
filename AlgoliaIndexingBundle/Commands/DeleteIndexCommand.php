<?php declare(strict_types=1);

namespace FroshAlgolia\AlgoliaIndexingBundle\Commands;

use Shopware\Commands\ShopwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DeleteIndexCommand.
 */
class DeleteIndexCommand extends ShopwareCommand
{
    protected function configure()
    {
        $this
            ->setName('algolia:index:delete')
            ->addArgument('indexName', InputArgument::REQUIRED);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->container->get('algolia_client')->deleteIndex($input->getArgument('indexName'));

        $output->writeln(sprintf('Index with name "%s" has been successfully deleted', $input->getArgument('indexName')));
    }
}
