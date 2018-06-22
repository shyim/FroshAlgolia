<?php declare(strict_types=1);

namespace FroshAlgolia\Commands;

use Shopware\Commands\ShopwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ListIndexCommand.
 */
class ListIndexCommand extends ShopwareCommand
{
    protected function configure()
    {
        $this
            ->setName('algolia:index:list')
            ->setDescription('List all indexes');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders([
            'Name',
            'Created at',
            'Updated at',
            'Entries',
        ]);

        foreach ($this->container->get('algolia_client')->listIndexes()['items'] as $listIndex) {
            $table->addRow([
                $listIndex['name'],
                $listIndex['createdAt'],
                $listIndex['updatedAt'],
                $listIndex['entries'],
            ]);
        }

        $table->render();
    }
}
