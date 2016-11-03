<?php
/**
 * Created by PhpStorm.
 * User: mfuerst
 * Date: 26.07.2016
 * Time: 17:12
 */

namespace SwAlgolia\Subscriber;

use \Enlight\Event\SubscriberInterface;

class AlgoliaSearchSubscriber implements SubscriberInterface
{
    /**
     * @return array
     *
     * Return an array of all subscribed events in this class
     */
    public static function getSubscribedEvents()
    {
        return array(
            'Enlight_Controller_Action_PostDispatch_Frontend' => 'initAlgoliaSearch',
            'Theme_Compiler_Collect_Plugin_Less' => 'onCollectPluginLess'
        );
    }

    /**
     * Create view variables for Algolia search
     * @param \Enlight_Controller_ActionEventArgs $args
     */
    public function initAlgoliaSearch(\Enlight_Controller_ActionEventArgs $args)
    {
        $pluginConfig = Shopware()->Container()->get('shopware.plugin.cached_config_reader')->getByPluginName($this->getName());
        $controller = $args->get('subject');
        $view = $controller->View();
        $view->assign('algoliaApplicationId', $pluginConfig['algolia-application-id']);
        $view->assign('algoliaSearchOnlyApiKey', $pluginConfig['algolia-search-only-api-key']);
        $view->assign('indexName', $pluginConfig['index-name']);
    }

}