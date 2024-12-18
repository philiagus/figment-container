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

namespace Philiagus\Figment\Container\Resolver\Proxy;

use Philiagus\Figment\Container\ContainerException;
use Philiagus\Figment\Container\Contract\Resolver;
use Traversable;

readonly class TypedInstanceProxy implements Resolver, \IteratorAggregate
{
    /**
     * @param Resolver $resolver
     * @param \Closure|string $type
     */
    public function __construct(
        private Resolver              $resolver,
        private \Closure|string|array $type
    )
    {
    }

    public function resolve(): object
    {
        $result = $this->resolver->resolve();
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

    public function getIterator(): Traversable
    {
        yield $this;
    }
}
