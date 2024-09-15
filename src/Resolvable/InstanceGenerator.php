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

namespace Philiagus\Figment\Container\Resolvable;


use Philiagus\Figment\Container\ContainerException;
use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Injector;

/**
 * @internal
 */
class InstanceGenerator extends AbstractInstanceConfigurator
{

    private object $singleton;

    private bool $running = false;

    public function __construct(
        Contract\Configuration    $configuration,
        private readonly \Closure $generator
    )
    {
        parent::__construct($configuration);
    }

    public function resolve(): object
    {
        if (!isset($this->singleton)) {
            if ($this->running)
                throw new \RuntimeException("Recursion while resolving instance of generator");

            $this->running = true;
            try {
                $injector = new Injector($this);
                $instance = ($this->generator)($injector);
                if ($injector->isSingletonEnabled()) {
                    $this->singleton = $instance;
                }
                try {
                    $injector->execute($this);
                } catch (\Throwable $e) {
                    unset($this->singleton);
                    throw new ContainerException(
                        "Instantiation of service via generator failed",
                        previous: $e
                    );
                }
            } finally {
                $this->running = false;
            }

            return $instance;
        }

        return $this->singleton;
    }
}
