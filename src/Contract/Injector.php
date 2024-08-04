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
use Philiagus\Figment\Container\Contract\List\InstanceList;
use Philiagus\Parser\Contract\Chainable;
use Philiagus\Parser\Contract\Parser;
use Philiagus\Parser\Parser\Extraction\Assign;

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
     * @param bool $disableSingleton If true the configuration used to create the object
     *                               will be instructed to try and create a new instance
     *                               instead of using the one potentially already stored in
     *                               its singleton
     * @return self
     * @see InstanceConfigurator::exposeAs()
     * @see InstanceConfigurator::redirectInstance()
     */
    public function instance(string $name, mixed &$target, bool $disableSingleton = false): self;

    /**
     * Requests a list via its name as exposed to the container. The resolved list
     * will then be put into the targeted variable, which is most times a property of the class.
     *
     * The resulting object will always be an InstanceList, so any property that will
     * hold the result should be typed as `?InstanceList`.
     *
     * @param string $name
     * @param mixed &$target The target variable/property to put the requested list in
     * @return self
     * @see InstanceList
     * @see InstanceConfigurator::redirectList()
     */
    public function list(string $name, mixed &$target): self;

    /**
     * Requests a context from the environment and puts it directly into $target via reference.
     *
     * @param string $name
     * @param mixed &$target
     * @return self
     * @see InstanceConfigurator::setContext()
     */
    public function context(string $name, mixed &$target): self;

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
}
