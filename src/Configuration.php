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

namespace Philiagus\Figment\Container;

use Philiagus\Figment\Container\Instance\InstanceClass;
use Philiagus\Figment\Container\Instance\InstanceGenerator;
use Philiagus\Figment\Container\Instance\InstanceObject;
use Philiagus\Figment\Container\List\LazyInstanceResolver;
use Philiagus\Figment\Container\List\LazyListResolver;
use Philiagus\Figment\Container\List\ListConfigurator;

class Configuration implements Contract\Configuration
{

    private array $lists = [];
    private array $instances = [];

    private readonly \Closure $instanceExposer;
    private readonly \Closure $listExposer;

    public function __construct()
    {
        $this->instanceExposer = function (string $name, Contract\Instance\InstanceResolver $resolver): void {
            if (isset($this->instances[$name]))
                throw new \LogicException(
                    "Trying to expose an instance of name '$name' twice"
                );

            $this->instances[$name] = $resolver;
        };

        $this->listExposer = function (string $name, Contract\List\ListResolver $resolver): void {
            if (isset($this->lists[$name]))
                throw new \LogicException(
                    "Trying to expose a list of name '$name' twice"
                );

            $this->lists[$name] = $resolver;
        };
    }

    public function instanceObject(object $object): Contract\Instance\InstanceResolver&Contract\Instance\InstanceExposer
    {
        return new InstanceObject($this->instanceExposer, $object);
    }

    public function exposedList(string $name): Contract\List\ListResolver
    {
        return $this->lists[$name] ?? new LazyListResolver(
            function () use ($name) {
                return ($this->lists[$name] ?? throw new \OutOfBoundsException(
                    "Instance of name '$name' is not exposed by the container"
                ))->resolve();
            }
        );
    }

    public function exposedInstance(string $name): Contract\Instance\InstanceResolver
    {
        return $this->instances[$name] ?? new LazyInstanceResolver(
            function (bool $disableSingleton) use ($name) {
                return ($this->instances[$name] ?? throw new \OutOfBoundsException(
                    "Instance of name '$name' is not exposed by the container"
                ))->resolve($disableSingleton);
            }
        );
    }

    public function instanceClass(string $className): Contract\Instance\InstanceConfigurator
    {
        return new InstanceClass($this, $this->instanceExposer, $className);
    }

    public function list(Contract\Instance\InstanceResolver|Contract\List\ListResolver ...$content): Contract\List\ListConfigurator
    {
        return new ListConfigurator($this->listExposer, $content);
    }

    public function instanceGenerator(\Closure $generator): Contract\Instance\InstanceConfigurator
    {
        return new InstanceGenerator($this, $this->instanceExposer, $generator);
    }
}
