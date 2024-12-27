<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Integration\AllSingletons;

use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Contract\Container;
use Philiagus\Figment\Container\Enum\SingletonMode;

class Factory implements Contract\Factory
{
    public function __construct(
        private SingletonMode $singletonMode,
    ){}
    public function create(Container $container, string $id): object
    {
        return new \stdClass();
    }

    public function getSingletonMode(string $id): SingletonMode
    {
        return $this->singletonMode;
    }
}
