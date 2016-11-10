<?php

namespace SwAlgolia\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Controller_Action;
use Enlight_Event_EventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
    ){
        $this->viewDir = $viewDir;
        $this->container = $container;
    }

    /**
     * @return array
     *
     * Return an array of all subscribed events in this class
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatch_Frontend' => 'initAlgoliaSearch'
        ];
    }

    /**
     * Create view variables for Algolia search
     * @param Enlight_Event_EventArgs $args
     */
    public function initAlgoliaSearch(Enlight_Event_EventArgs $args)
    {
        $pluginConfig = $this->container->get('shopware.plugin.cached_config_reader')->getByPluginName('SwAlgolia');
        $shopId = $this->container->get('router')->getContext()->getShopId();

        /** @var Enlight_Controller_Action $controller */
        $controller = $args->get('subject');
        $view = $controller->View();

        $view->addTemplateDir($this->viewDir);
        $view->assign('algoliaApplicationId', $pluginConfig['algolia-application-id']);
        $view->assign('algoliaSearchOnlyApiKey', $pluginConfig['algolia-search-only-api-key']);
        $view->assign('indexName', $pluginConfig['index-prefix-name'].'-'.$shopId);
        $view->assign('showAlgoliaLogo',$pluginConfig['show-algolia-logo']);
    }
}
