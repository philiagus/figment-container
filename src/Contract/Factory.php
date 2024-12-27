<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Contract;

use Philiagus\Figment\Container\Enum\SingletonMode;

interface Factory
{

    public function create(Container $container, string $id): object;

    public function getSingletonMode(string $id): SingletonMode;

}
