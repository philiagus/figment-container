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
use Philiagus\Figment\Container\InstanceList;

class ListBuilder implements Contract\Builder\ListBuilder, \IteratorAggregate
{

    /** @var array<array<string|Contract\Builder>> */
    private array $contents = [];

    public function __construct(
        private readonly Contract\Configuration $configuration
    )
    {
    }

    public function build(string $name): object
    {
        return new InstanceList($name, ...$this);
    }

    public function append(Contract\Builder|string ...$resolver): static
    {
        $this->contents[] = $resolver;

        return $this;
    }

    public function merge(Contract\Builder ...$resolver): static
    {
        foreach ($resolver as $singleResolver) {
            $this->contents[] = $singleResolver;
        }

        return $this;
    }

    public function registerAs(string ...$id): Contract\Builder\Registrable
    {
        $this->configuration->register($this, ...$id);

        return $this;
    }

    public function getIterator(): \Traversable
    {
        foreach ($this->contents as $content) {
            foreach ($content as $element) {
                if (is_string($element)) {
                    yield $this->configuration->get($element);
                } else {
                    yield $element;
                }
            }
        }
    }
}
