<?php

declare(strict_types=1);

use Shapecode\Bundle\HiddenEntityTypeBundle\Form\Type\HiddenObjectType;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(HiddenObjectType::class);
};
