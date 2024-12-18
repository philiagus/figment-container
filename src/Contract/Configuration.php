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

interface Configuration extends Provider
{
    public function buildContainer(): Container;

    public function register(Resolver $resolver, string ...$id): self;

    public function class(string $className): InstanceConfigurator;

    public function generator(bool $useSingleton, \Closure $closure): Registrable;

    public function object(object $object): Registrable;

    public function list(?string $id = null): ListConfigurator;
}
