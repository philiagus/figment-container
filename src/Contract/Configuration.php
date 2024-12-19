<?php
/*
 * This file is part of philiagus/figment-container
 *
 * (c) Andreas Eicher <philiagus@philiagus.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Philiagus\Figment\Container\Contract;

use Philiagus\Figment\Container\Contract\Configuration\ConstructedConfigurator;
use Philiagus\Figment\Container\Contract\Configuration\InjectionConfigurator;
use Philiagus\Figment\Container\Contract\Configuration\ListConfigurator;
use Philiagus\Figment\Container\Contract\Configuration\Registrable;

interface Configuration extends Provider
{
    public function buildContainer(): Container;

    public function register(Resolver $resolver, string ...$id): self;

    public function injected(string $className): InjectionConfigurator;

    public function constructed(string $className): ConstructedConfigurator;

    public function generator(bool $useSingleton, \Closure $closure): Registrable&Resolver;

    public function object(object $object): Registrable&Resolver;

    public function list(?string $id = null): ListConfigurator;
}
