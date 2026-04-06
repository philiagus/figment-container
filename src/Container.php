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

namespace Philiagus\Figment\Container;

use Philiagus\Figment\Container\Contract\Context;
use Philiagus\Figment\Container\Exception\ContainerException;

/**
 * @internal
 */
readonly final class Container implements Contract\Container
{

    public function __construct(private Contract\BuilderContainer $provider)
    {
    }

    /** @inheritDoc */
    #[\Override]
    public function class(string $className): object
    {
        return $this->get($className, $className);
    }

    /** @inheritDoc */
    #[\Override]
    public function get(string $id, ?string $className = null): object
    {
        $instance = $this->provider->get($id)->build($id);
        if ($className === null || $instance instanceof $className) {
            return $instance;
        }

        throw new ContainerException(
            "Result of class call for id $id did not result in instance of $className"
        );
    }

    /** @inheritDoc */
    #[\Override]
    public function has(string $id): bool
    {
        return $this->provider->has($id);
    }

    /** @inheritDoc */
    #[\Override]
    public function context(): Context
    {
        return $this->provider->context();
    }

    /** @inheritDoc */
    #[\Override]
    public function invoke(\Closure $closure, ...$additionalArguments): mixed
    {
        return $this
            ->prepare($closure, ...array_keys($additionalArguments))
            ->invoke(...$additionalArguments);
    }

    /** @inheritDoc */
    #[\Override]
    public function prepare(\Closure $closure, string ...$laterProvidedArguments): Contract\PreparedFunction
    {
        return new PreparedFunction($this, $closure, ...$laterProvidedArguments);
    }

    /** @inheritDoc */
    #[\Override]
    public function instance(string $className, array $parameters = []): object
    {
        return $this->provider->instance($className, $parameters)->build($className);
    }
}
