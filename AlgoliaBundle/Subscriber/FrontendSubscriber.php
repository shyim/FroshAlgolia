<?php declare(strict_types=1);

namespace FroshAlgolia\AlgoliaBundle\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Controller_Action;
use Enlight_Event_EventArgs;
use Enlight_View_Default;
use FroshAlgolia\AlgoliaBundle\Service\ConfigReaderInterface;
use FroshAlgolia\AlgoliaBundle\Service\IndexNameBuilderInterface;
use Shopware_Components_Snippet_Manager as Snippets;

/**
 * Class AlgoliaSearchSubscriber.
 */
class FrontendSubscriber implements SubscriberInterface
{
    /**
     * @var string
     */
    private $viewDir;
    /**
     * @var array
     */
    private $pluginConfig;
    /**
     * @var IndexNameBuilderInterface
     */
    private $indexNameBuilder;
    /**
     * @var ConfigReaderInterface
     */
    private $configReader;

    /**
     * @var Snippets
     */
    private $snippets;

    /**
     * AlgoliaSearchSubscriber constructor.
     *
     * @param string                    $viewDir
     * @param IndexNameBuilderInterface $indexNameBuilder
     * @param array                     $pluginConfig
     * @param ConfigReaderInterface     $configReader
     * @param Snippets                  $snippets
     */
    public function __construct(
        string $viewDir,
        $indexNameBuilder,
        array $pluginConfig,
        ConfigReaderInterface $configReader,
        Snippets $snippets
    ) {
        $this->viewDir = $viewDir;
        $this->indexNameBuilder = $indexNameBuilder;
        $this->pluginConfig = $pluginConfig;
        $this->configReader = $configReader;
        $this->snippets = $snippets;
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
        $shop = Shopware()->Shop();

        /** @var Enlight_Controller_Action $controller */
        $controller = $args->get('subject');
        $view = $controller->View();

        // Assign data to view
        $view->addTemplateDir($this->viewDir);
        $view->assign('algoliaApplicationId', $this->pluginConfig['algolia-application-id']);
        $view->assign('algoliaSearchOnlyApiKey', $this->pluginConfig['algolia-search-only-api-key']);
        $view->assign('indexName', $this->indexNameBuilder->buildName($shop));
        $view->assign('showAlgoliaLogo', $this->pluginConfig['show-algolia-logo']);
        $this->assignTemplateVars($view);
    }

    private function assignTemplateVars(Enlight_View_Default $view): void
    {
        $view->algoliaConfig = $this->configReader->read(Shopware()->Shop());

        /**
         * Build the JS index for sort order based on replica configuration. First element in this
         * index is the main Algolia index.
         */
        $sortOrderArray = [
            [
                'name' => $this->indexNameBuilder->buildName(Shopware()->Shop()),
                'label' => $this->snippets->getNamespace('bundle/translation')->get('sort_order_default'),
            ],
        ];
        $replicaIndices = explode('|', $this->pluginConfig['index-replicas-custom-ranking-attributes']);

        foreach ($replicaIndices as $replicaIndex) {
            $replicaIndexSettings = explode(',', $replicaIndex);

            // Build the key / name for the replica index
            $nameElements = explode('(', $replicaIndexSettings[0]);
            $replicaIndexName = $this->indexNameBuilder->buildName(Shopware()->Shop()) . '_' . rtrim($nameElements[1], ')') . '_' . $nameElements[0];

            $sortOrderArray[] = [
                'name' => $replicaIndexName, // The index which is used for this sort order
                'label' => $this->snippets->getNamespace('bundle/translation')->get('sort_order_' . rtrim($nameElements[1], ')') . '_' . $nameElements[0]), // The name which should be shown to the customer
            ];
        }

        $sortOrderIndex = htmlspecialchars(json_encode($sortOrderArray, JSON_HEX_APOS), ENT_QUOTES | ENT_HTML5);
        $view->assign('sortOrderIndex', $sortOrderIndex);
    }
}
