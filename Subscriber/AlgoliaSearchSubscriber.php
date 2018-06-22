<?php declare(strict_types=1);

namespace FroshAlgolia\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Controller_Action;
use Enlight_Event_EventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AlgoliaSearchSubscriber.
 */
class AlgoliaSearchSubscriber implements SubscriberInterface
{
    /** @var string */
    private $pluginName;

    /** @var string */
    private $viewDir;

    /** @var ContainerInterface */
    private $container;

    /** @var array */
    private $pluginConfig;

    /**
     * AlgoliaSearchSubscriber constructor.
     *
     * @param string             $viewDir
     * @param ContainerInterface $container
     */
    public function __construct(
        $pluginName,
        $viewDir,
        ContainerInterface $container
    ) {
        $this->pluginName = $pluginName;
        $this->viewDir = $viewDir;
        $this->container = $container;
        $this->pluginConfig = $this->container->get('shopware.plugin.cached_config_reader')->getByPluginName('FroshAlgolia');
    }

    /**
     * Return an array of all subscribed events in this class.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatch_Frontend' => 'initAlgoliaSearch',
        ];
    }

    /**
     * Create view variables for Algolia search.
     *
     * @param Enlight_Event_EventArgs $args
     */
    public function initAlgoliaSearch(Enlight_Event_EventArgs $args)
    {
        $syncHelperService = $this->container->get('frosh_algolia.sync_helper_service');
        $shop = Shopware()->Shop();

        /** @var Enlight_Controller_Action $controller */
        $controller = $args->get('subject');
        $view = $controller->View();

        // Assign data to view
        $view->addTemplateDir($this->viewDir);
        $view->assign('algoliaApplicationId', $this->pluginConfig['algolia-application-id']);
        $view->assign('algoliaSearchOnlyApiKey', $this->pluginConfig['algolia-search-only-api-key']);
        $view->assign('indexName', $syncHelperService->buildIndexName($shop));
        $view->assign('showAlgoliaLogo', $this->pluginConfig['show-algolia-logo']);
        $view->assign('showAutocompletePrice', $this->pluginConfig['show-autocomplete-price']);
        $this->assignTemplateVars($view);
    }

    private function assignTemplateVars($view)
    {
        $pluginConfig = $this->container->get('shopware.plugin.cached_config_reader')->getByPluginName('FroshAlgolia');
        $syncHelperService = $this->container->get('frosh_algolia.sync_helper_service');
        $view->algoliaConfig = $this->container->get('frosh_algolia.config_reader')->read(Shopware()->Shop());

        /**
         * Build the JS index for sort order based on replica configuration. First element in this
         * index is the main Algolia index.
         */
        $sortOrderArray = [
            [
                'name' => $syncHelperService->buildIndexName(Shopware()->Shop()), // The index which is used for this sort order
                'label' => Shopware()->Snippets()->getNamespace('bundle/translation')->get('sort_order_default'), // The name which should be shown to the customer
            ],
        ];
        $replicaIndices = explode('|', $pluginConfig['index-replicas-custom-ranking-attributes']);

        foreach ($replicaIndices as $replicaIndex) {
            $replicaIndexSettings = explode(',', $replicaIndex);

            // Build the key / name for the replica index
            $nameElements = explode('(', $replicaIndexSettings[0]);
            $replicaIndexName = $syncHelperService->buildIndexName(Shopware()->Shop()) . '_' . rtrim($nameElements[1], ')') . '_' . $nameElements[0];

            $sortOrderArray[] = [
                'name' => $replicaIndexName, // The index which is used for this sort order
                'label' => Shopware()->Snippets()->getNamespace('bundle/translation')->get('sort_order_' . rtrim($nameElements[1], ')') . '_' . $nameElements[0]), // The name which should be shown to the customer
            ];
        }

        $sortOrderIndex = htmlspecialchars(json_encode($sortOrderArray, JSON_HEX_APOS));
        $view->assign('sortOrderIndex', $sortOrderIndex);
    }
}
