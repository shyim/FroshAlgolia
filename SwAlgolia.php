<?php

namespace SwAlgolia;

use Doctrine\Common\Collections\ArrayCollection;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Theme\LessDefinition;


class SwAlgolia extends Plugin
{

    /**
     * @param InstallContext $context
     * This method is called on plugin installation
     */
    public function install(InstallContext $context)
    {
        return parent::install($context);
    }

    /**
     * @param UpdateContext $context
     * This method is called on update of the plugin
     */
    public function update(UpdateContext $context)
    {
        return parent::update($context);
    }

    /**
     * @param ActivateContext $context
     * This method is called on activation of the plugin
     */
    public function activate(ActivateContext $context)
    {
        return parent::activate($context);
    }

    /**
     * @param DeactivateContext $context
     * This method is called on deactivation of the plugin
     */
    public function deactivate(DeactivateContext $context)
    {
        return parent::deactivate($context);
    }

    /**
     * @param UninstallContext $context
     * This method is called once on uninstallation of the plugin
     */
    public function uninstall(UninstallContext $context)
    {
        return parent::uninstall($context);
    }

    /**
     * @return array
     * Required for adding the register subscriber event before dispatching
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Front_StartDispatch' => 'registerSubscriber',
            'Enlight_Controller_Action_PostDispatchSecure' => 'addTemplateDir',
            'Theme_Compiler_Collect_Plugin_Less' => 'collectPluginLess',
            'Theme_Compiler_Collect_Plugin_Javascript' => 'collectJavascriptFiles',
            'Enlight_Controller_Action_PostDispatch_Frontend' => 'initAlgolia'
        ];
    }

    /**
     * Set the template Dir for all requests
     */
    public function addTemplateDir() {
        $this->container->get('template')->addTemplateDir(__DIR__ . '/Views');
    }

    public function initAlgolia(\Enlight_Controller_ActionEventArgs $args) {
        $pluginConfig = Shopware()->Container()->get('shopware.plugin.cached_config_reader')->getByPluginName($this->getName());
        $controller = $args->get('subject');
        $view = $controller->View();
        $view->assign('algoliaApplicationId',$pluginConfig['algolia-application-id']);
        $view->assign('algoliaSearchOnlyApiKey',$pluginConfig['algolia-search-only-api-key']);
        $view->assign('indexName',$pluginConfig['index-name']);
    }

    /**
     * @param \Enlight_Controller_EventArgs $args
     * Add all subscriber classes for events here
     */
    public function registerSubscriber(\Enlight_Controller_EventArgs $args)
    {

    }

    /**
     * @return \Shopware\Components\Theme\LessDefinition
     *
     * This method delivers the .less file for the added HTML elements to the Shopware .less Compiler. So this
     * .less will be automatically get integrated in the main CSS as soon as the theme is compiled.
     */
    public function collectPluginLess() {

        return new LessDefinition(
            [],
            [__DIR__.'/Views/frontend/_public/src/less/style.less']
        );

    }

    /**
     * This method delivers the JS files to the Shopware Theme Compiler. So this
     * JS files will be automatically get integrated in the main CSS as soon as the theme is compiled.
     *
     * @return ArrayCollection
     */
    public function collectJavascriptFiles()
    {
        $jsDir = __DIR__ . '/Views/frontend/_public/src/js/';

        return new ArrayCollection(array());
    }

}
