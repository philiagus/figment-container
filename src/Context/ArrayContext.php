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

namespace Philiagus\Figment\Container\Context;

use Philiagus\Figment\Container\Contract\Context\Provider;

/**
 * Context class representing the configuration provided as an array
 */
readonly class ArrayContext implements Provider
{

    /**
     * All keys of the array are treated as keys of the context with their corresponding values
     *
     * @param array $context
     */
    public function __construct(
        private array $context
    )
    {

    }

    /** @inheritDoc */
    public function has(string $name): bool
    {
        return array_key_exists($name, $this->context);
    }

    /** @inheritDoc */
    public function get(string $name): mixed
    {
        return array_key_exists($name, $this->context) ?
            $this->context[$name] :
            throw new \OutOfBoundsException("Trying to access undefined context '$name'");
    }
}
