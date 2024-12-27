<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Builder\Proxy;

use Philiagus\Figment\Container\Contract;

readonly class RedirectionProxy implements Contract\Builder, \IteratorAggregate
{

    public function __construct(
        private Contract\BuilderContainer $builderContainer,
        private string|Contract\Builder $target
    )
    {
    }

    public function build(string $id): object
    {
        if ($this->target instanceof Contract\Builder) {
            try {
                return $this->target->build($id);
            } catch (Contract\ContainerTraceException $e) {
                $e->prependContainerTrace("$id -redirect-> nameless builder");
            }
        }

        try {
            return $this->builderContainer->get($this->target)->build($this->target);
        } catch (Contract\ContainerTraceException  $e) {
            $e->prependContainerTrace("$id redirected to $this->target");
        }
    }

    public function getIterator(): \Traversable
    {
        yield $this;
    }

    public function exists(): bool
    {
        return $this->target instanceof Contract\Builder || $this->builderContainer->has($this->target);
    }
}
