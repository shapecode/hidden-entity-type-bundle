<?php

namespace Shapecode\Bundle\HiddenEntityTypeBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class ShapecodeHiddenEntityTypeExtension
 *
 * @package Shapecode\Bundle\HiddenEntityTypeBundle\DependencyInjection
 * @author  Nikita Loges
 */
class ShapecodeHiddenEntityTypeExtension extends Extension
{

    /**
     * @inheritdoc
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $locator = new FileLocator(__DIR__ . '/../Resources/config');
        $loader = new Loader\YamlFileLoader($container, $locator);
        $loader->load('form.yml');
    }
}
