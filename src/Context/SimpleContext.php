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

use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Exception\UndefinedContextException;

/**
 * A context that simply maps the received names to keys of a provided array
 *
 * The following array would yield `123` for the requested key `root.value`
 *
 * <code>
 * [
 *     'root' => [
 *         'value' => 'This lower level will never be checked'
 *     ],
 *     'root.value' => 123
 * ]
 * </code>
 */
readonly class SimpleContext implements Contract\Context
{

    /**
     * @param array<string, mixed> $context Array containing the context values
     */
    public function __construct(private array $context)
    {
    }

    /** @inheritDoc */
    #[\Override]
    public function has(string $name): bool
    {
        return array_key_exists($name, $this->context);
    }

    /** @inheritDoc */
    #[\Override]
    public function get(string $name): mixed
    {
        return $this->context[$name] ?? throw new UndefinedContextException($name);
    }
}
