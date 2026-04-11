<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container;

use Philiagus\Figment\Container\Helper\TypeCheck;

/**
 * @internal
 */
readonly final class InstantiatedInstanceList implements Contract\InstanceList, \IteratorAggregate
{
    private array $content;

    public function __construct(object ...$content)
    {
        $this->content = array_values($content);
    }

    /** @inheritDoc */
    #[\Override]
    public function count(): int
    {
        return count($this->content);
    }

    /** @inheritDoc */
    #[\Override]
    public function traverseBuilders(array|string|\Closure|null $type = null): \Traversable
    {
        foreach ($this->content as $content) {
            yield new Builder\Proxy\TypeCheckObjectProxy($content, $type);
        }
    }

    /** @inheritDoc */
    #[\Override]
    public function traverseInstances(array|string|\Closure|null $type = null): \Traversable
    {
        if ($type === null) {
            yield from $this->content;
        } else foreach ($this->content as $content) {
            TypeCheck::assertType($type, $content);
            yield $content;
        }
    }

    /** @inheritDoc */
    #[\Override]
    public function getIterator(): \Traversable
    {
        yield from $this->content;
    }
}
