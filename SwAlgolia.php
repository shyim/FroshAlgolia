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
use Symfony\Component\DependencyInjection\ContainerBuilder;

require_once 'vendor/autoload.php';

/**
 * Class SwAlgolia
 * Plugin main class for Algolia search
 * @package SwAlgolia
 */
class SwAlgolia extends Plugin
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->setParameter('sw_algolia.plugin_dir', $this->getPath());
    }
}
