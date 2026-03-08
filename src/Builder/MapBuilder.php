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

namespace Philiagus\Figment\Container\Builder;

use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Contract\Builder;
use Philiagus\Figment\Container\InstanceMap;

/**
 * @internal
 */
class MapBuilder implements Contract\Builder\MapBuilder, \IteratorAggregate
{

    /** @var array<Contract\Builder> */
    private array $builders = [];

    /** @var array */
    private array $keys = [];

    /**
     * @param Contract\Configuration $configuration
     */
    public function __construct(
        private readonly Contract\Configuration $configuration
    )
    {
    }

    /** @inheritDoc */
    #[\Override]
    public function build(string $id): object
    {
        return new InstanceMap(
            $id,
            $this->keys,
            $this->builders
        );
    }

    /** @inheritDoc */
    #[\Override]
    public function registerAs(string ...$id): Contract\Builder\Registrable
    {
        $this->configuration->register($this, ...$id);

        return $this;
    }

    /** @inheritDoc */
    #[\Override]
    public function getIterator(): \Traversable
    {
        foreach ($this->keys as $index => $key) {
            yield $key => $this->builders[$index];
        }
    }

    public function set(mixed $key, string|Builder $builder): static
    {
        $index = array_search($key, $this->keys, true);
        if (is_string($builder)) {
            $builder = new Proxy\RedirectionProxy($this->configuration, $builder);
        }
        if ($index === false) {
            $this->keys[] = $key;
            $this->builders[] = $builder;

            return $this;
        } else {
            $this->builders[$index] = $builder;
        }

        return $this;
    }
}
