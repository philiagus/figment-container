<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Enum;

enum SingletonMode
{

    /**
     * Completely disables the singleton handling of the builder
     * Essentially, every time the container requests an instance from the
     * builder, the builder will create and return a new instance
     */
    case DISABLED;

    /**
     * Enables the singleton to have a single instance for this builder
     * No matter how many different IDs are requested from this builder, they
     * all will return the same instance
     */
    case BY_BUILDER;

    /**
     * Enables the singleton to have a single instance per requested id from the
     * builder
     */
    case BY_ID;

    /**
     * Returns a unique string to be used as the key of an array containing the
     * singletons for the provided id, respecting the singleton rules.
     *
     * It is assumed that the singletons are stored within the context of
     * a single builder
     *
     * @param string $forId
     *
     * @return null|string
     *
     * @internal
     */
    public function resolve(string $forId): null|string
    {
        return match ($this) {
            self::DISABLED => null,
            self::BY_BUILDER => "*",
            self::BY_ID => "=$forId"
        };
    }

}
