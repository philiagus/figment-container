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
use Philiagus\Figment\Container\Contract\Configuration\OverwriteConstructorParameterProvider;

readonly class ClassReflection
{

    public bool $singletonDisabled;
    private \ReflectionClass $class;
    private ?\ReflectionMethod $constructor;

    /** @var array<string, array{\ReflectionParameter, Contract\InjectionAttribute[]}> */
    private array $constructorParameters;

    public function __construct(private string $className)
    {
        $this->class = new \ReflectionClass($className);
        $this->constructor = $this->class->getConstructor();
        $this->singletonDisabled = !empty($this->class->getAttributes(DisableSingleton::class));
        $constructorParameters = [];
        if ($this->constructor) foreach ($this->constructor->getParameters() as $parameter) {
            $attributes = $parameter->getAttributes(
                Contract\InjectionAttribute::class,
                \ReflectionAttribute::IS_INSTANCEOF
            );
            if ($attributes) {
                $constructorParameters[$parameter->getName()] = [
                    $parameter,
                    array_map(
                        static fn(\ReflectionAttribute $o) => $o->newInstance(),
                        $attributes
                    )
                ];
            }
        }
        $this->constructorParameters = $constructorParameters;
    }

    public function ghostInjected(Contract\Provider&OverwriteConstructorParameterProvider $provider): object
    {
        return $this->class->newLazyGhost(
            function (object $object) use ($provider) {
                if ($this->constructor) {
                    $constructorArguments = $provider->resolveOverwriteConstructorParameter();
                    foreach ($this->constructorParameters as $name => [$parameter, $attributes]) {
                        if (array_key_exists($name, $constructorArguments)) {
                            continue;
                        }
                        /** @var Contract\InjectionAttribute $attribute */
                        foreach ($attributes as $attribute) {
                            $hasValue = false;
                            $value = $attribute->resolve($provider, $parameter, $hasValue);
                            if ($hasValue) {
                                $constructorArguments[$name] = $value;
                                break;
                            }
                        }
                    }
                    $this->constructor->invokeArgs($object, $constructorArguments);
                }
            }
        );
    }

    public function ghostConstructed(OverwriteConstructorParameterProvider $parameterProvider): object
    {
        if ($this->constructor) {
            return $this->class->newLazyGhost(function (object $object) use ($parameterProvider) {
                $this->constructor->invokeArgs($object, $parameterProvider->resolveOverwriteConstructorParameter());
            });
        }
        return new $this->className();
    }
}
