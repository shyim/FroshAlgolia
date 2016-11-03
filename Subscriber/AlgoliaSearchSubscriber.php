<?php
/**
 * Created by PhpStorm.
 * User: mfuerst
 * Date: 26.07.2016
 * Time: 17:12
 */

namespace SwAlgolia\Subscriber;

use \Enlight\Event\SubscriberInterface;
use Enlight_Controller_Action;
use Enlight_Event_EventArgs;

class AlgoliaSearchSubscriber implements SubscriberInterface
{
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
        $pluginConfig = Shopware()->Container()->get('shopware.plugin.cached_config_reader')->getByPluginName('SwAlgolia');

        /** @var Enlight_Controller_Action $controller */
        $controller = $args->get('subject');
        $view = $controller->View();
        $view->assign('algoliaApplicationId', $pluginConfig['algolia-application-id']);
        $view->assign('algoliaSearchOnlyApiKey', $pluginConfig['algolia-search-only-api-key']);
        $view->assign('indexName', $pluginConfig['index-name']);
    }
}
