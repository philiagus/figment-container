<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Integration\AllInjections;

use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Contract\Container;

class Factory implements Contract\Factory {

    public function create(Container $container, string $id): object
    {
        return new InfoDTO($id, 'FACTORY');
    }
}
