<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Integration\Circular;

use Philiagus\Figment\Container\Configuration;
use Philiagus\Figment\Container\Contract\Container;

$config = new Configuration();

$config
    ->injected(MockClass::class)
    ->redirect('child', 'b')
    ->registerAs('a');

$config
    ->factory(new TargetedFactory('c'))
    ->registerAs('b');

$config
    ->closure(fn(Container $container) => $container->get('d'))
    ->registerAs('c');

$config
    ->injected(MockClass::class)
    ->redirect('child', 'e')
    ->registerAs('d');

$config
    ->factory(new TargetedFactory('f'))
    ->registerAs('e');

$config
    ->closure(fn(Container $container) => $container->get('a'))
    ->registerAs('f');

return $config;
