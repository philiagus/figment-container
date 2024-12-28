<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Builder\Proxy;

use Philiagus\Figment\Container\Contract;

/**
 * @internal
 */
readonly class RedirectionProxy implements Contract\Builder, \IteratorAggregate
{

    public function __construct(
        private Contract\BuilderContainer $builderContainer,
        private string|Contract\Builder $target
    )
    {
    }

    /** @inheritDoc */
    #[\Override]
    public function build(string $id): object
    {
        if ($this->target instanceof Contract\Builder) {
            try {
                return $this->target->build($id);
            } catch (Contract\PrependMessageThrowableInterface $e) {
                $e->prependMessage("$id redirected to nameless builder");
            }
        }

        try {
            return $this->builderContainer->get($this->target)->build($this->target);
        } catch (Contract\PrependMessageThrowableInterface  $e) {
            $e->prependMessage("$id redirected to $this->target");
        }
    }

    /** @inheritDoc */
    #[\Override]
    public function getIterator(): \Traversable
    {
        yield $this;
    }

    /**
     * @return bool
     */
    public function exists(): bool
    {
        return $this->target instanceof Contract\Builder || $this->builderContainer->has($this->target);
    }
}
