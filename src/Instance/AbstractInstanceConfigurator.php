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

namespace Philiagus\Figment\Container\Instance;

use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Contract\Instance\InstanceResolver;
use Philiagus\Figment\Container\Contract\List\InstanceList;
use Philiagus\Figment\Container\Contract\List\ListResolver;

abstract class AbstractInstanceConfigurator
    implements Contract\Instance\InstanceConfigurator,
    Contract\Container,
    InstanceResolver
{

    private array $instanceRedirection = [];
    private array $listRedirection = [];
    private array|Contract\Context\Provider $context = [];

    protected function __construct(
        private readonly Contract\ResolverProvider $container,
        private readonly \Closure                  $exposer
    )
    {
    }


    public function redirectInstance(string $from, Contract\Instance\InstanceResolver $to): self
    {
        $this->instanceRedirection[$from] = $to;

        return $this;
    }

    public function redirectList(string $from, Contract\List\ListResolver $to): self
    {
        $this->listRedirection[$from] = $to;

        return $this;
    }

    /** @inheritDoc */
    public function exposeAs(string $name): Contract\Instance\InstanceExposer
    {
        ($this->exposer)($name, $this);

        return $this;
    }

    public function list(string $name): InstanceList
    {
        return $this->listResolver($name)->resolve();
    }

    public function listResolver(string $name): ListResolver
    {
        return $this->listRedirection[$name] ?? $this->container->listResolver($name);
    }

    public function instance(string $name, bool $disableSingleton = false): object
    {
        return $this->instanceResolver($name)->resolve($disableSingleton);
    }

    public function instanceResolver(string $name): InstanceResolver
    {
        return $this->instanceRedirection[$name] ?? $this->container->instanceResolver($name);
    }

    public function instantiate(string $className, Contract\Context\Provider $context): object
    {
        return $this->container->instantiate($className, $context);
    }

    public function getContext(string $name): mixed
    {
        if (is_array($this->context)) {
            return array_key_exists($name, $this->context) ?
                $this->context[$name] :
                throw new \OutOfBoundsException("Trying to access undefined context '$name'");
        }
        return $this->context->get($name);
    }

    public function setContext(array|Contract\Context\Provider $context): self
    {
        $this->context = $context;

        return $this;
    }
}
