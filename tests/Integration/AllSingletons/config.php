<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Integration\AllSingletons;

use Philiagus\Figment\Container;

$config = new Container\Configuration();
$config
    ->attributed(SingletonById::class)
    ->registerAs(
        'injected.by-id.0',
        'injected.by-id.1',
        'injected.by-id.2',
        'injected.by-id.3',
    );

$config->attributed(SingletonByBuilder::class)
    ->registerAs(
        'injected.by-builder.0',
        'injected.by-builder.1',
        'injected.by-builder.2',
        'injected.by-builder.3',
    );

$config
    ->attributed(SingletonDisabled::class)
    ->registerAs(
        'injected.disabled.0',
        'injected.disabled.1',
        'injected.disabled.2',
        'injected.disabled.3',
    );

$config
    ->constructed(SingletonById::class)
    ->registerAs(
        'constructed.by-id.0',
        'constructed.by-id.1',
        'constructed.by-id.2',
        'constructed.by-id.3',
    );

$config
    ->constructed(SingletonByBuilder::class)
    ->registerAs(
        'constructed.by-builder.0',
        'constructed.by-builder.1',
        'constructed.by-builder.2',
        'constructed.by-builder.3',
    );

$config
    ->constructed(SingletonDisabled::class)
    ->registerAs(
        'constructed.disabled.0',
        'constructed.disabled.1',
        'constructed.disabled.2',
        'constructed.disabled.3',
    );

$config
    ->constructed(\stdClass::class)
    ->singletonMode(Container\Enum\SingletonMode::DISABLED)
    ->registerAs(
        'constructed.set.disabled.0',
        'constructed.set.disabled.1',
        'constructed.set.disabled.2',
        'constructed.set.disabled.3',
    );

$config
    ->constructed(\stdClass::class)
    ->singletonMode(Container\Enum\SingletonMode::BY_ID)
    ->registerAs(
        'constructed.set.by-id.0',
        'constructed.set.by-id.1',
        'constructed.set.by-id.2',
        'constructed.set.by-id.3',
    );

$config
    ->constructed(\stdClass::class)
    ->singletonMode(Container\Enum\SingletonMode::BY_BUILDER)
    ->registerAs(
        'constructed.set.by-builder.0',
        'constructed.set.by-builder.1',
        'constructed.set.by-builder.2',
        'constructed.set.by-builder.3',
    );

$config
    ->factory(new Factory(Container\Enum\SingletonMode::DISABLED))
    ->registerAs(
        'factory.disabled.0',
        'factory.disabled.1',
        'factory.disabled.2',
        'factory.disabled.3',
    );

$config
    ->factory(new Factory(Container\Enum\SingletonMode::BY_ID))
    ->registerAs(
        'factory.by-id.0',
        'factory.by-id.1',
        'factory.by-id.2',
        'factory.by-id.3',
    );

$config
    ->factory(new Factory(Container\Enum\SingletonMode::BY_BUILDER))
    ->registerAs(
        'factory.by-builder.0',
        'factory.by-builder.1',
        'factory.by-builder.2',
        'factory.by-builder.3',
    );

$config
    ->constructed(Factory::class)
    ->parameterSet('singletonMode', Container\Enum\SingletonMode::DISABLED)
    ->registerAs('factory.disabled');

$config
    ->constructed(Factory::class)
    ->parameterSet('singletonMode', Container\Enum\SingletonMode::BY_ID)
    ->registerAs('factory.by-id');

$config
    ->constructed(Factory::class)
    ->parameterSet('singletonMode', Container\Enum\SingletonMode::BY_BUILDER)
    ->registerAs('factory.by-builder');

$config
    ->factory('factory.disabled')
    ->registerAs(
        'get-factory.disabled.0',
        'get-factory.disabled.1',
        'get-factory.disabled.2',
        'get-factory.disabled.3',
    );

$config
    ->factory('factory.by-id')
    ->registerAs(
        'get-factory.by-id.0',
        'get-factory.by-id.1',
        'get-factory.by-id.2',
        'get-factory.by-id.3',
    );

$config
    ->factory('factory.by-builder')
    ->registerAs(
        'get-factory.by-builder.0',
        'get-factory.by-builder.1',
        'get-factory.by-builder.2',
        'get-factory.by-builder.3',
    );


return $config;

