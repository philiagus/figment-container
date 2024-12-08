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

    private array|Contract\Context\Provider $specificContext = [];

    private bool $fallbackToDefault = false;

    protected function __construct(
        private readonly Contract\Configuration          $configuration,
        private readonly array|Contract\Context\Provider $defaultContext
    )
    {
    }


    public function redirect(string $from, Contract\Resolvable|string $to): self
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

    public function getContextDefaulted(string $name, mixed $default): mixed
    {
        $value = $this->readContext($name, $found);
        if ($found) {
            return $value;
        }

        return $default;
    }

    /**
     * @param string $name
     * @param null $found
     * @return mixed
     */
    private function readContext(string $name, null &$found): mixed
    {
        $found = true;
        if (is_array($this->specificContext)) {
            if (array_key_exists($name, $this->specificContext)) {
                return $this->specificContext[$name];
            }
        } elseif ($this->specificContext->has($name)) {
            return $this->specificContext->get($name);
        }

        if ($this->fallbackToDefault) {
            if (is_array($this->defaultContext)) {
                if (array_key_exists($name, $this->defaultContext)) {
                    return $this->defaultContext[$name];
                }
            } elseif ($this->defaultContext->has($name)) {
                return $this->defaultContext->get($name);
            }
        }

        $found = false;
        return null;
    }

    /** @inheritDoc */
    public function has(string $id): bool
    {
        return isset($this->redirection[$id]) || $this->configuration->has($id);
    }

    /** @inheritDoc */
    public function get(string $id): object
    {
        $resolvable = $this->redirection[$id] ?? $this->configuration->get($id);
        if (is_string($resolvable))
            $resolvable = $this->configuration->get($resolvable);
        return $resolvable->resolve();
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getContext(string $name): mixed
    {
        $result = $this->readContext($name, $found);
        if ($found)
            return $result;

        throw new \OutOfBoundsException("Trying to access undefined context '$name'");
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasContext(string $name): bool
    {
        $hasSpecific = is_array($this->specificContext) ?
            array_key_exists($name, $this->specificContext) :
            $this->specificContext->has($name);

        if (!$hasSpecific && !$this->fallbackToDefault)
            return $hasSpecific;

        return is_array($this->defaultContext) ?
            array_key_exists($name, $this->defaultContext) :
            $this->defaultContext->has($name);
    }

    /** @inheritDoc */
    public function context(array|Contract\Context\Provider $context, bool $fallbackToDefault = false): self
    {
        $this->specificContext = $context;
        $this->fallbackToDefault = $fallbackToDefault;

        return $this;
    }

    /** @inheritDoc */
    public function getIterator(): \Traversable
    {
        yield $this;
    }
}
