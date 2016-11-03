<?php

namespace SwAlgolia\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Template_Manager;

class FrontendSubscriber implements SubscriberInterface
{
    /**
     * @var string
     */
    private $viewDir;

    /**
     * @var Enlight_Template_Manager
     */
    private $template;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure' => 'addTemplateDir'
        ];
    }

    /**
     * FrontendSubscriber constructor.
     */
    public function __construct($viewDir, Enlight_Template_Manager $template)
    {
        $this->viewDir = $viewDir;
        $this->template = $template;
    }

    /**
     * Set the template Dir for all requests
     */
    public function addTemplateDir() {
        $this->template->addTemplateDir($this->viewDir);
    }
}
