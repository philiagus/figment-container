<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Contract\Builder;

use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Exception\ContainerConfigurationException;

interface OverwriteConstructorParameterReceiver
{
    /**
     * Defines the target constructor parameter to contain the defined value
     *
     * @param non-empty-string $name
     * @param mixed $value
     *
     * @return $this
     *
     * @throws ContainerConfigurationException
     */
    public function parameterSet(string $name, mixed $value): static;

    /**
     * Defines the target constructor parameter to use injection
     *
     * The Builder can either be a builder specifically created for this purpose
     * or the name of a registered builder taken from the injection, taking
     * redirects into account (if applicable)
     *
     * @param non-empty-string $name
     * @param string|Contract\Builder $injection
     *
     * @return $this
     *
     * @throws ContainerConfigurationException
     */
    public function parameterInject(string $name, string|Contract\Builder $injection): static;

    /**
     * Defines the target constructor parameter to be filled from the context
     *
     * @param non-empty-string $name
     * @param string $contextName
     *
     * @return $this
     *
     * @throws ContainerConfigurationException
     */
    public function parameterContext(string $name, string $contextName): static;

    /**
     * Defines the target constructor parameter to be filled with the currently
     * resolved id
     *
     * @param non-empty-string $name
     *
     * @return $this
     *
     * @throws ContainerConfigurationException
     */
    public function parameterId(string $name): static;

    /**
     * Defines the targeted constructor parameter to be filled with the result
     * of the Closure. The Closure is called on demand and every time as needed
     * and must return the value to use for the parameter. Any exception thrown
     * by the Closure will continue to bubble through the container and will
     * _not_ be caught in place.
     *
     * @param non-empty-string $name
     * @param \Closure(Contract\Container $container, string $forId): mixed $generator
     *
     * @return $this
     */
    public function parameterGenerate(string $name, \Closure $generator): static;
}
