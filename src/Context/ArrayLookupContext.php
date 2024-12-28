<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Context;

use Philiagus\Figment\Container\Contract\Context;
use Philiagus\Figment\Container\Exception\UndefinedContextException;

/**
 * Context that iterates through the levels of an array to find the required
 * context name.
 *
 * The following array would yield `123` for the name `root.sub`
 *
 * <code>
 * [
 *     'root' => [
 *         'sub' => 123
 *     ]
 * ]
 * </code>
 */
readonly class ArrayLookupContext implements Context
{
    /**
     * @param array<string, mixed> $context
     * @param string $separator The string at which any name should be cut and
     *                          then recursively searched in the provided array
     */
    public function __construct(
        private array $context,
        private string $separator = '.'
    )
    {
    }

    /** @inheritDoc */
    #[\Override]
    public function has(string $name): bool
    {
        $path = explode($this->separator, $name);
        $current = $this->context;
        foreach ($path as $element) {
            if (!is_array($current) || !array_key_exists($element, $current)) {
                return false;
            }
            $current = $current[$element];
        }
        return true;
    }

    /** @inheritDoc */
    #[\Override]
    public function get(string $name): mixed
    {
        $path = explode($this->separator, $name);
        $current = $this->context;
        foreach ($path as $element) {
            if (!is_array($current) || !array_key_exists($element, $current)) {
                throw new UndefinedContextException($name);
            }
            $current = $current[$element];
        }
        return $current;
    }
}
