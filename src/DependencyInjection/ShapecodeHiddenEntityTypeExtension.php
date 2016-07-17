<?php

namespace Shapecode\Bundle\HiddenEntityTypeBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class ShapecodeHiddenEntityType
 *
 * @package Shapecode\Bundle\HiddenEntityTypeBundle\DependencyInjection
 * @author  Nikita Loges
 */
class ShapecodeHiddenEntityType extends Extension
{
    /**
     * @inheritdoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $bundles = $container->getParameter('kernel.bundles');

        if (isset($bundles['DoctrineBundle'])) {
            $loader->load('doctrine_orm.yml');
        }
        if (isset($bundles['DoctrineMongoDBBundle'])) {
            $loader->load('doctrine_mongodb.yml');
        }
    }
}
