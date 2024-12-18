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

namespace Philiagus\Figment\Container\ReflectionRegistry;

use Philiagus\Figment\Container\Attribute\DisableSingleton;
use Philiagus\Figment\Container\Contract;

readonly class ClassReflection
{

    public \ReflectionClass $class;
    public ?\ReflectionMethod $constructor;
    public bool $singletonDisabled;
    public array $properties;


    public function __construct(string $className) {
        $this->class = new \ReflectionClass($className);
        $constructor = null;
        $properties = [];
        $singletonDisabled = false;
        $iterationClass = $this->class;
        do {
            if ($iterationClass->getAttributes(DisableSingleton::class)) {
                $singletonDisabled = true;
            }
            $constructor ??= $iterationClass->getConstructor();
            foreach ($iterationClass->getProperties() as $property) {
                $attributes = $property->getAttributes(
                    Contract\InjectionAttribute::class,
                    \ReflectionAttribute::IS_INSTANCEOF
                );
                if (!$attributes) {
                    continue;
                }
                $properties[] = [
                    $property,
                    array_map(
                        fn(\ReflectionAttribute $o) => $o->newInstance(),
                        $attributes
                    )
                ];
            }
        } while ($iterationClass = $iterationClass->getParentClass());
        $this->constructor = $constructor;
        $this->singletonDisabled = $singletonDisabled;
        $this->properties = $properties;
    }
}
