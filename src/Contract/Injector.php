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

namespace Philiagus\Figment\Container\Contract;

use Philiagus\Figment\Container\Contract\Instance\InstanceConfigurator;
use Philiagus\Parser\Contract\Chainable;
use Philiagus\Parser\Contract\Parser;

/**
 * The injector is the class that is used to inject dependencies, lists and context into
 * objects instantiated by the instance configuration.
 *
 * @see Injectable
 */
interface Injector
{
    /**
     * Request an instance via its name as exposed to the container. The resolved
     * object will then be put into the targeted variable, which is most times a property
     * of the class.
     *
     * To ensure this working with how PHP handles references the properties _must be_ defined as
     * nullable.
     *
     * @param string $name
     * @param mixed &$target The target variable/property to put the requested instance in
     *
     * @return self
     * @see InstanceConfigurator::exposeAs()
     * @see InstanceConfigurator::redirectInstance()
     */
    public function inject(string $name, mixed &$target): self;

    /**
     * Requests a context from the environment and puts it directly into $target via reference.
     *
     * @param string $name
     * @param mixed &$target
     * @return self
     * @see InstanceConfigurator::context()
     */
    public function context(string $name, mixed &$target): self;

    /**
     * Requests a context from the environment and puts it directly into $target via reference.
     * If the context name is not set in the environment, the default is used instead
     *
     * @param string $name
     * @param mixed &$target
     * @param mixed $default
     * @return self
     * @see InstanceConfigurator::context()
     */
    public function contextDefaulted(string $name, mixed $default, mixed &$target): self;

    /**
     * Requests a setting from the provided context and provides it to the provided parser.
     * This can be used to assert certain rules about the provided context by using
     * the parsers.
     *
     * You can use the Assign parser extraction in order to write the provided value
     * into a property of the object.
     *
     * @param string $name
     * @param Parser $parser
     * @return self
     * @see Chainable::thenAssignTo()
     * @see Assign
     */
    public function parseContext(string $name, Parser $parser): self;

    /**
     * Requests a setting from the provided context and provides it to the provided parser.
     * This can be used to assert certain rules about the provided context by using
     * the parsers.
     *
     * You can use the Assign parser extraction in order to write the provided value
     * into a property of the object.
     *
     * If the context is not set in the environment, the default is used instead
     *
     * @param string $name
     * @param mixed $default
     * @param Parser $parser
     * @return self
     * @see Chainable::thenAssignTo()
     * @see Assign
     */
    public function parseContextDefaulted(string $name, mixed $default, Parser $parser): self;

    /**
     * Configures the service to not use singleton - essentially this means
     * that whenever the container is asked for an instance of this service
     * the container will create a new one.
     *
     * @return self
     */
    public function disableSingleton(): self;
}
