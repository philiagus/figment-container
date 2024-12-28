<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Context;

use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Exception\UndefinedContextException;

readonly class MappingContext implements Contract\Context
{

    /**
     * @param Contract\Context $source
     * @param array<string, string> $map
     * A map, mapping the names that reach this context to the names to be
     * requested from the source context
     * @param bool $allowUnmappedNames
     * If false only mapped values will be tried against the sourceContext
     * If true, requested names not listed in the map will be searched in the
     * source as is
     * @param bool $fallbackToUnmapped
     * If true and the value is not found under its mapped name the source
     * context will be asked to provide exactly the requested name
     */
    public function __construct(
        private Contract\Context $source,
        private array $map,
        private bool $allowUnmappedNames = false,
        private bool $fallbackToUnmapped = false
    )
    {
        if (array_any($map, static fn($element) => !is_string($element))) {
            throw new \InvalidArgumentException(
                '$map of MappingContext must be an array<string, string>'
            );
        }
    }

    /** @inheritDoc */
    #[\Override]
    public function get(string $name): mixed
    {
        $mapped = $this->map[$name] ?? null;
        if ($mapped === null) {
            if ($this->allowUnmappedNames) {
                return $this->source->get($name);
            }

            throw new UndefinedContextException($name);
        }

        if ($this->source->has($mapped)) {
            return $this->source->get($mapped);
        }

        if ($this->fallbackToUnmapped) {
            return $this->source->get($name);
        }

        throw new UndefinedContextException($name);
    }

    /** @inheritDoc */
    #[\Override]
    public function has(string $name): bool
    {
        $mapped = $this->map[$name] ?? null;
        if ($mapped === null) {
            if ($this->allowUnmappedNames) {
                return $this->source->has($name);
            }

            return false;
        }

        return $this->source->has($mapped) || (
                $this->fallbackToUnmapped &&
                $this->source->has($name)
            );
    }
}
