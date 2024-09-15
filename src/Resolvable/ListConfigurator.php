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

use IteratorAggregate;
use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Contract\Configuration;
use Philiagus\Figment\Container\Contract\Resolvable;
use Traversable;

/**
 * Implementation of the ListConfigurator interface
 *
 * @internal
 */
class ListConfigurator implements Contract\List\ListConfigurator, IteratorAggregate
{

    /** @var Resolvable[] */
    private array $content;

    /**
     * @param Configuration $configuration
     * @param Resolvable[] $content
     */
    public function __construct(
        private readonly Contract\Configuration $configuration,
        Resolvable                              ...$content
    )
    {
        $this->content = array_values($content);
    }

    public function exposeAs(string ...$id): Contract\Exposable
    {
        $this->configuration->expose($this, ...$id);

        return $this;
    }

    public function append(Resolvable ...$instance): Contract\List\ListConfigurator
    {
        $this->content = [...$this->content, ...array_values($instance)];

        return $this;
    }

    public function resolve(): Contract\List\InstanceList
    {
        return new InstanceList($this->content);
    }

    public function getIterator(): Traversable
    {
        yield from $this->content;
    }
}
