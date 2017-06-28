<?php

namespace SwAlgolia\Subscriber;

use Doctrine\Common\Collections\ArrayCollection;
use Enlight\Event\SubscriberInterface;
use Shopware\Components\Theme\LessDefinition;

/**
 * Class AssetsSubscriber.
 */
class AssetsSubscriber implements SubscriberInterface
{
    /**
     * @var string
     */
    private $viewDir;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Theme_Compiler_Collect_Plugin_Less' => 'collectPluginLess',
            'Theme_Compiler_Collect_Plugin_Javascript' => 'collectJavascriptFiles',
        ];
    }

    /**
     * AssetsSubscriber constructor.
     *
     * @param string $viewDir
     */
    public function __construct($viewDir)
    {
        $this->viewDir = $viewDir;
    }

    /**
     * @return LessDefinition
     *
     * This method delivers the .less file for the added HTML elements to the Shopware .less Compiler. So this
     * .less will be automatically get integrated in the main CSS as soon as the theme is compiled.
     */
    public function collectPluginLess()
    {
        return new LessDefinition(
            [],
            [
                $this->viewDir.'/frontend/_public/src/less/autocomplete.less',
                $this->viewDir.'/frontend/_public/src/less/instantsearch.less',
            ]
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
        return new ArrayCollection([
            $this->viewDir.'/frontend/_public/src/js/jquery.swalgolia.js',
        ]);
    }
}
