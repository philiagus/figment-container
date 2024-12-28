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

/**
 * @internal
 */
readonly class ObjectBuilder implements Contract\Builder\ObjectBuilder, \IteratorAggregate
{

    /**
     * @param Contract\Configuration $configuration
     * @param object $object
     */
    public function __construct(
        private Contract\Configuration $configuration,
        private object $object
    )
    {
    }

    /** @inheritDoc */
    #[\Override]
    public function build(string $id): object
    {
        return $this->object;
    }

    /** @inheritDoc */
    #[\Override]
    public function registerAs(string ...$id): Contract\Builder\Registrable
    {
        $this->configuration->register($this, ...$id);

        return $this;
    }

    /** @inheritDoc */
    #[\Override]
    public function getIterator(): \Traversable
    {
        yield $this;
    }
}
