<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Context;

use Philiagus\Figment\Container\Contract\Context;
use Philiagus\Figment\Container\Exception\UndefinedContextException;

readonly class ArrayLookupContext implements Context
{
    public function __construct(
        private array $context,
        private string $separator = '.'
    )
    {
    }

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
