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
use Philiagus\Figment\Container\Contract\InstanceConfigurationExposer;
use Philiagus\Figment\Container\Injector;

class InstanceGenerator extends AbstractInstanceConfigurator
{

    private object $singleton;

    private bool $running = false;

    public function __construct(
        Contract\ResolverProvider $container,
        \Closure                  $exposer,
        private readonly \Closure $generator
    )
    {
        parent::__construct($container, $exposer);
    }

    public function resolve(bool $disableSingleton = false): object
    {
        if ($disableSingleton || !isset($this->singleton)) {
            if ($this->running)
                throw new \RuntimeException("Recursion while resolving instance of generator");

            $this->running = true;
            try {
                $injector = new Injector($this);
                $this->singleton = ($this->generator)($injector);
                try {
                    $injector->execute();
                } catch (\Throwable $e) {
                    unset($this->singleton);
                    throw $e;
                }
            } finally {
                $this->running = false;
            }
        }

        return $this->singleton;
    }
}
