<?php

namespace SwAlgolia\Services\DependencyInjection\CompilerPass;

use Shopware\Components\DependencyInjection\Compiler\TagReplaceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Class SettingsCompilerPass
 * @package SwAlgolia\Services\DependencyInjection\CompilerPass
 */
class SettingsCompilerPass implements CompilerPassInterface
{
    use TagReplaceTrait;

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->replaceArgumentWithTaggedServices($container, 'swalgolia.shop_indexer_factory', 'swalgolia.settings', 2);
    }
}
