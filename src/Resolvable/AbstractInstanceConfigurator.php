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

namespace Philiagus\Figment\Container\Resolvable;

use Philiagus\Figment\Container\Contract;
use Traversable;

/**
 * @internal
 */
abstract class AbstractInstanceConfigurator
    implements Contract\Instance\InstanceConfigurator,
    Contract\Container,
    Contract\Resolvable,
    \IteratorAggregate
{

    /** @var array<string, Contract\Resolvable> */
    private array $redirection = [];
    private array|Contract\Context\Provider $context = [];

    protected function __construct(
        private readonly Contract\Configuration $configuration,
    )
    {
    }


    public function redirect(string $from, Contract\Resolvable $to): self
    {
        $this->redirection[$from] = $to;

        return $this;
    }

    /** @inheritDoc */
    public function exposeAs(string ...$id): Contract\Exposable
    {
        $this->configuration->expose($this, ...$id);

        return $this;
    }

    public function getContext(string $name): mixed
    {
        if (is_array($this->context)) {
            return array_key_exists($name, $this->context) ?
                $this->context[$name] :
                throw new \OutOfBoundsException("Trying to access undefined context '$name'");
        }
        return $this->context->get($name);
    }

    public function get(string $id): object
    {
        $resolvable = $this->redirection[$id] ?? $this->configuration->get($id);
        return $resolvable->resolve();
    }

    public function context(array|Contract\Context\Provider $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function has(string $id): bool
    {
        return isset($this->redirection[$id]) || $this->configuration->has($id);
    }

    public function getIterator(): Traversable
    {
        throw new \LogicException("Trying to iterate over a single object instance resolver");
    }
}
