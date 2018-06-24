<?php declare(strict_types=1);

namespace FroshAlgolia\AlgoliaIndexingBundle\Commands;

use FroshAlgolia\AlgoliaIndexingBundle\Service\BacklogSyncInterface;
use Shopware\Commands\ShopwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncBacklogCommand extends ShopwareCommand
{
    /**
     * @var BacklogSyncInterface
     */
    private $backlogSync;

    /**
     * SyncBacklogCommand constructor.
     *
     * @param BacklogSyncInterface $backlogSync
     */
    public function __construct(BacklogSyncInterface $backlogSync)
    {
        parent::__construct('');
        $this->backlogSync = $backlogSync;
    }

    public function configure()
    {
        $this
            ->setName('algolia:backlog:sync')
            ->setDescription('Sync backlog entries to algolia index')
            ->addArgument('limit', InputArgument::OPTIONAL, 'Max backlog entries per sync', 100);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->backlogSync->sync($input->getArgument('limit'));
    }
}
