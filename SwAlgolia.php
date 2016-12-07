<?php

namespace SwAlgolia;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use SwAlgolia\Bootstrap\Schemas;
use Symfony\Component\DependencyInjection\ContainerBuilder;

// Composer autoload
require_once(__DIR__.'/vendor/autoload.php');

/**
 * Class SwAlgolia
 *
 * Plugin main class for SwAlgolia Algolia (https://www.algolia.com) search plugin
 */
class SwAlgolia extends Plugin
{
    /**
     * @param InstallContext $context
     */
    public function install(InstallContext $context)
    {
        Schemas::createSchemas();

        parent::install($context);
    }

    /**
     * @param UninstallContext $context
     */
    public function uninstall(UninstallContext $context)
    {
        Schemas::removeSchemas();

        parent::uninstall($context);
    }

    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->setParameter('sw_algolia.plugin_dir', $this->getPath());
    }
}
