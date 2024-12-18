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

interface InstanceConfigurator extends Registrable, Resolver, Provider
{
    public function setContext(Context|array $context, bool $fallbackToDefault = false): self;

    public function redirect(string $id, Resolver|string $resolver): self;

    public function constructorArguments(mixed ...$params): self;
}
