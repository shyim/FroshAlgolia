<?php declare(strict_types=1);

namespace FroshAlgolia\AlgoliaIndexingBundle\Commands;

use FroshAlgolia\AlgoliaIndexingBundle\Service\SyncServiceInterface;
use FroshAlgolia\Services\SyncService;
use Shopware\Commands\ShopwareCommand;
use Shopware\Models\Shop\Shop;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SyncCommand.
 */
class SyncCommand extends ShopwareCommand
{
    /**
     * @var SyncServiceInterface
     */
    private $syncService;

    /**
     * @param SyncServiceInterface $syncService
     */
    public function __construct(
        SyncServiceInterface $syncService
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
            ->setDescription('Sync shopware products to algolia index');
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $shops = $this->container->get('models')->getRepository(Shop::class)->getActiveShops();

        $this->syncService->fullSync($shops);
    }
}
