<?php

namespace SwAlgolia\Services\DependencyInjection\CompilerPass;

use Shopware\Components\DependencyInjection\Compiler\TagReplaceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Class SynchronizerCompilerPass
 * @package SwAlgolia\Services\DependencyInjection\CompilerPass
 */
class SynchronizerCompilerPass implements CompilerPassInterface
{
    use TagReplaceTrait;

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->replaceArgumentWithTaggedServices($container, 'swalgolia.composite_synchronizer_factory', 'swalgolia.synchronizer', 0);
    }
}
