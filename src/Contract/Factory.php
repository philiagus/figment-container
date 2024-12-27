<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Contract;

interface Factory
{

    public function create(Container $container, string $id): object;

}
