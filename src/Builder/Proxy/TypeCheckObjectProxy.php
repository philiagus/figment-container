<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Builder\Proxy;

use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Helper\TypeCheckTrait;

/**
 * @internal
 */
readonly class TypeCheckObjectProxy implements Contract\Builder, \IteratorAggregate
{
    use TypeCheckTrait;

    /**
     * @param object $result
     * @param null|class-string[]|Closure|string $type
     */
    public function __construct(
        private object $result,
        private null|array|\Closure|string $type
    )
    {
    }

    /** @inheritDoc */
    #[\Override]
    public function build(string $id): object
    {
        if ($this->type !== null) {
            $this->assertType($this->type, $this->result);
        }
        return $this->result;
    }

    /** @inheritDoc */
    #[\Override]
    public function getIterator(): \Traversable
    {
        yield $this;
    }
}
