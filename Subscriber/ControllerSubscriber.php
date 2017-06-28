<?php

namespace SwAlgolia\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;

/**
 * Class ControllerSubscriber.
 */
class ControllerSubscriber implements SubscriberInterface
{
    /**
     * @var string
     */
    private $pluginDir;

    public function __construct($pluginDir)
    {
        $this->pluginDir = $pluginDir;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Dispatcher_ControllerPath_Frontend_Search' => 'getController',
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_Algolia' => 'getController',
        ];
    }

    /**
     * @param Enlight_Event_EventArgs $args
     *
     * @return string
     */
    public function getController(Enlight_Event_EventArgs $args)
    {
        list($module, $controller) = explode('_', str_replace('Enlight_Controller_Dispatcher_ControllerPath_', '', $args->getName()));

        return $this->pluginDir.'/Controllers/'.$module.'/'.$controller.'.php';
    }
}
