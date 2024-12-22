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
use Philiagus\Figment\Container\Exception\ContainerException;

readonly class TypeCheckProxy implements Builder, \IteratorAggregate
{
    /**
     * @param Builder $builder
     * @param class-string|class-string[]|\Closure(object $object): bool $type
     */
    public function __construct(
        private Builder               $builder,
        private \Closure|string|array $type
    )
    {
    }

    public function build(string $name): object
    {
        $result = $this->builder->build($name);
        if ($this->type instanceof \Closure) {
            if (!($this->type)($result)) {
                throw new ContainerException(
                    "Instance of class " . $result::class . " did not resolve to the expected class"
                );
            }
        } elseif (is_string($this->type)) {
            if (!$result instanceof $this->type) {
                throw new ContainerException(
                    "Instance of class " . $result::class . " is not instance of {$this->type}"
                );
            }
        } else {
            $matches = false;
            foreach ($this->type as $type) {
                if ($result instanceof $type) {
                    $matches = true;
                    break;
                }
            }
            if (!$matches) {
                throw new ContainerException(
                    "Instance of class " . $result::class . " is not instance of " . implode('|', $this->type)
                );
            }
        }
        return $result;

    }

    public function getIterator(): \Traversable
    {
        yield $this;
    }
}
