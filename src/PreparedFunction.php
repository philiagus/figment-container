<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container;

use Philiagus\Figment\Container\Exception\ContainerException;

/**
 * @internal
 */
readonly class PreparedFunction implements Contract\PreparedFunction
{


    private array $arguments;

    public function __construct(
        Contract\Container $container,
        private \Closure $function,
        array $definedArguments,
        array $skippedParameters
    )
    {
        $invokeArguments = [];
        $reflection = new \ReflectionFunction($this->function);
        foreach ($reflection->getParameters() as $parameter) {
            $parameterName = $parameter->getName();
            if (in_array($parameterName, $skippedParameters)) continue;
            $hasValue = false;
            $value = null;
            if (array_key_exists($parameterName, $definedArguments)) {
                $hasValue = true;
                $value = $definedArguments[$parameterName];
            } else {
                $attributes = $parameter->getAttributes(
                    Contract\InjectionAttribute::class,
                    \ReflectionAttribute::IS_INSTANCEOF
                );
                foreach ($attributes as $attribute) {
                    /** @var Contract\InjectionAttribute $attributeInstance */
                    $attributeInstance = $attribute->newInstance();
                    $value = $attributeInstance->resolve(
                        $container, $parameter, "parameter value", $hasValue
                    );
                    if ($hasValue) break;
                }
            }
            if (!$hasValue && !$parameter->isOptional()) {
                throw new ContainerException(
                    "Could not create parameter value for not-optional function parameter '$parameterName'"
                );
            }

            $invokeArguments[$parameterName] = $value;
        }
        $this->arguments = $invokeArguments;
    }

    public function invoke(mixed ...$additionalArguments): mixed
    {
        return ($this->function)(...($additionalArguments + $this->arguments));
    }

    public function __invoke(mixed ...$additionalArguments): mixed
    {
        return ($this->function)(...($additionalArguments + $this->arguments));
    }

}
