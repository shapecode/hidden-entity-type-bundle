<?php

declare(strict_types=1);

namespace Shapecode\Bundle\HiddenEntityTypeBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

final class ShapecodeHiddenEntityTypeExtension extends Extension
{
    /** @inheritDoc */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $locator = new FileLocator(__DIR__ . '/../Resources/config');
        $loader  = new PhpFileLoader($container, $locator);
        $loader->load('services.php');
    }
}
