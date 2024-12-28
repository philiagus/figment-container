<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Helper;

use Philiagus\Figment\Container\Exception\ContainerException;

/**
 * @internal
 */
trait TypeCheckTrait
{

    /**
     * @param \Closure|class-string|array<class-string> $type
     * @param object $object
     *
     * @return void
     * @throws ContainerException
     */
    protected function assertType(\Closure|string|array $type, object $object): void
    {
        if ($type instanceof \Closure) {
            if (!$type($object)) {
                throw new ContainerException(
                    "Instance of class " . $object::class . " does not match the expectation"
                );
            }
        } elseif (is_string($type)) {
            if (!$object instanceof $type) {
                throw new ContainerException(
                    "Instance of class " . $object::class . " is not instance of $type"
                );
            }
        } else {
            $matches = array_any(
                $type,
                static fn($subType) => $object instanceof $subType
            );
            if (!$matches) {
                throw new ContainerException(
                    "Instance of class " . $object::class . " is not instance of " . implode('|', $type)
                );
            }
        }
    }


}
