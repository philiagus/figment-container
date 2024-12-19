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

namespace Philiagus\Figment\Container\Resolver;

use Philiagus\Figment\Container\Contract\Configuration;
use Philiagus\Figment\Container\Contract\Configuration\ListConfigurator;
use Philiagus\Figment\Container\Contract\Configuration\Registrable;
use Philiagus\Figment\Container\Contract\Resolver;
use Traversable;

class ListConfiguration implements ListConfigurator, \IteratorAggregate {

    /** @var array<array<string|Resolver>> */
    private array $contents = [];

    public function __construct(
        private readonly Configuration $configuration
    ) {}

    public function resolve(): object
    {
        return new ListConfiguration\InstanceList(...$this);
    }

    public function append(Resolver|string ...$resolver): ListConfigurator
    {
        $this->contents[] = $resolver;

        return $this;
    }

    public function merge(Resolver ...$resolver): ListConfigurator
    {
        foreach($resolver as $singleResolver) {
            $this->contents[] = $singleResolver;
        }

        return $this;
    }

    public function registerAs(string ...$id): Registrable
    {
        $this->configuration->register($this, ...$id);

        return $this;
    }

    public function getIterator(): Traversable
    {
        foreach($this->contents as $content) {
            foreach($content as $element) {
                if(is_string($element)) {
                    yield $this->configuration->get($element);
                } else {
                    yield $element;
                }
            }
        }
    }
}
