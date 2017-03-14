<?php

namespace SwAlgolia\Subscriber;

use Doctrine\ORM\EntityManager;
use Enlight\Event\SubscriberInterface;
use Enlight_Controller_Action;
use Enlight_Event_EventArgs;
use Shopware\Models\Property\Option;
use Shopware\Models\Shop\Shop;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AlgoliaSearchSubscriber.
 */
class AlgoliaSearchSubscriber implements SubscriberInterface
{
    /**
     * @var string
     */
    private $viewDir;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * AlgoliaSearchSubscriber constructor.
     *
     * @param string             $viewDir
     * @param ContainerInterface $container
     */
    public function __construct(
        $viewDir,
        ContainerInterface $container
    ) {
        $this->viewDir = $viewDir;
        $this->container = $container;
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
        $pluginConfig = $this->container->get('shopware.plugin.cached_config_reader')->getByPluginName('SwAlgolia');
        $syncHelperService = $this->container->get('sw_algolia.sync_helper_service');
        $shop = Shopware()->Shop();

        /** @var Enlight_Controller_Action $controller */
        $controller = $args->get('subject');
        $view = $controller->View();

        // Assign data to view
        $view->addTemplateDir($this->viewDir);
        $view->assign('algoliaApplicationId', $pluginConfig['algolia-application-id']);
        $view->assign('algoliaSearchOnlyApiKey', $pluginConfig['algolia-search-only-api-key']);
        $view->assign('indexName', $syncHelperService->buildIndexName($shop));
        $view->assign('showAlgoliaLogo', $pluginConfig['show-algolia-logo']);
        $view->assign('showAutocompletePrice', $pluginConfig['show-autocomplete-price']);
    }
}
