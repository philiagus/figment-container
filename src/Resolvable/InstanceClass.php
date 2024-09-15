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
class InstanceClass extends AbstractInstanceConfigurator
{

    private object $singleton;

    private bool $validationTodo = true;

    private bool $running = false;

    public function __construct(
        Contract\Configuration  $configuration,
        private readonly string $className
    )
    {
        parent::__construct($configuration);
    }

    /** @inheritDoc */
    public function resolve(): object
    {
        if ($this->validationTodo) {
            if (!is_a($this->className, Contract\Injectable::class, true)) {
                throw new ContainerException(
                    "Class {$this->className} does not implement " . Contract\Injectable::class
                );
            }
            $this->validationTodo = false;
        }

        if (!isset($this->singleton)) {
            if ($this->running)
                throw new \RuntimeException("Recursion while resolving instance of '{$this->className}'");

            $this->running = true;
            try {
                $injector = new Injector($this);
                $instance = new ($this->className)($injector);
                if ($injector->isSingletonEnabled()) {
                    $this->singleton = $instance;
                }
                try {
                    $injector->execute($this);
                } catch (\Throwable $e) {
                    unset($this->singleton);
                    throw new ContainerException(
                        "Instantiation of service from class {$this->className} failed",
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
