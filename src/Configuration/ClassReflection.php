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

namespace Philiagus\Figment\Container\Configuration;

use Philiagus\Figment\Container\Attribute\DisableSingleton;
use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Contract\Builder\OverwriteConstructorParameterProvider;
use Philiagus\Figment\Container\Contract\Container;

/**
 * @internal
 */
readonly class ClassReflection implements Contract\Configuration\ClassReflection
{

    public bool $singletonDisabled;
    private \ReflectionClass $class;
    private ?\ReflectionMethod $constructor;

    /** @var array<string, array{\ReflectionParameter, Contract\InjectionAttribute[]}> */
    private array $constructorParameters;

    /**
     * @param class-string $className
     * @throws \ReflectionException
     */
    public function __construct(
        private string $className
    )
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

    /**
     * @param Container&OverwriteConstructorParameterProvider $provider
     * @param string $forName
     * @return object
     */
    public function buildInjected(Contract\Container&OverwriteConstructorParameterProvider $provider, string $forName): object
    {
        return $this->class->newLazyGhost(
            function (object $object) use ($provider, $forName) {
                if ($this->constructor) {
                    $constructorArguments = $provider->resolveOverwriteConstructorParameter($forName);
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

    /**
     * @param OverwriteConstructorParameterProvider $parameterProvider
     * @param string $forName
     * @return object
     */
    public function buildConstructed(OverwriteConstructorParameterProvider $parameterProvider, string $forName): object
    {
        if ($this->constructor) {
            return $this->class->newLazyGhost(
                fn(object $object) => $this->constructor->invokeArgs(
                    $object,
                    $parameterProvider->resolveOverwriteConstructorParameter($forName)
                )
            );
        }
        return new $this->className();
    }
}
