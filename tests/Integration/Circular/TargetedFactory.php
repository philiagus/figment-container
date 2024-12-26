<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Integration\Circular;

use Philiagus\Figment\Container\Contract\Container;
use Philiagus\Figment\Container\Contract\Factory;

class TargetedFactory implements Factory {

    public function __construct(private string $target) {

    }

    public function create(Container $container, string $name): object
    {
        return $container->get($this->target);
    }
}
