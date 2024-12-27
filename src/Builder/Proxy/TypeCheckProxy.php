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

namespace Philiagus\Figment\Container\Builder\Proxy;

use Philiagus\Figment\Container\Contract\Builder;
use Philiagus\Figment\Container\Helper\TypeCheckTrait;

readonly class TypeCheckProxy implements Builder, \IteratorAggregate
{
    use TypeCheckTrait;

    /**
     * @param Builder $builder
     * @param class-string|class-string[]|\Closure(object $object): bool $type
     */
    public function __construct(
        private Builder $builder,
        private \Closure|string|array $type
    )
    {
    }

    public function build(string $id): object
    {
        $result = $this->builder->build($id);
        $this->assertType($this->type, $result);
        return $result;
    }

    public function getIterator(): \Traversable
    {
        yield $this;
    }
}
