<?php

namespace SwAlgolia\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Controller_Action;
use Enlight_Event_EventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AlgoliaSearchSubscriber
 * @package SwAlgolia\Subscriber
 */
class AlgoliaSearchSubscriber implements SubscriberInterface
{

    /** @var  string */
    private $pluginName;

    /** @var string */
    private $viewDir;

    /** @var ContainerInterface */
    private $container;

    /** @var  array */
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
        $this->pluginConfig = $this->container->get('shopware.plugin.cached_config_reader')->getByPluginName('SwAlgolia');
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
        $syncHelperService = $this->container->get('sw_algolia.sync_helper_service');
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
    }
}
