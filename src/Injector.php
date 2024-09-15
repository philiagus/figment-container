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

use Philiagus\Figment\Container\Resolvable\AbstractInstanceConfigurator;
use Philiagus\Parser\Base\Subject;
use Philiagus\Parser\Contract\Parser;
use Philiagus\Parser\Exception\ParsingException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Implementation of the Injector interface.
 *
 * Should not be used outside this library
 *
 * @internal
 */
class Injector implements Contract\Injector
{

    private const int TYPE_INJECT = 1,
        TYPE_CONTEXT = 2,
        TYPE_CONTEXT_PARSE = 3;

    /** @var array<array{"0":int,"1":string,"2":mixed}> */
    private array $injections = [];

    private bool $singletonEnabled = true;

    /** @inheritDoc */
    public function context(string $name, mixed &$target): Contract\Injector
    {
        $this->injections[] = [self::TYPE_CONTEXT, $name, &$target];

        return $this;
    }

    /** @inheritDoc */
    public function parseContext(string $name, Parser $parser): Contract\Injector
    {
        $this->injections[] = [self::TYPE_CONTEXT_PARSE, $name, $parser];

        return $this;
    }

    /**
     * Executes the configuration of the Injector, injecting dependencies requested from the provided
     * instance configuration, where redirections and data sources are handled
     *
     * @param AbstractInstanceConfigurator $instanceConfiguration
     * @return void
     * @throws ParsingException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function execute(AbstractInstanceConfigurator $instanceConfiguration): void
    {
        foreach ($this->injections as [$type, $name, &$target]) {
            if ($type === self::TYPE_CONTEXT_PARSE) {
                /** @var Parser $target */
                $target->parse(Subject::default($instanceConfiguration->getContext($name), "Context '$name'"));
            } else {
                $target = match ($type) {
                    self::TYPE_INJECT => $instanceConfiguration->get($name),
                    self::TYPE_CONTEXT => $instanceConfiguration->getContext($name)
                };
            }
        }
    }

    /** @inheritDoc */
    public function inject(string $name, mixed &$target): Contract\Injector
    {
        $this->injections[] = [self::TYPE_INJECT, $name, &$target];

        return $this;
    }

    /**
     * Returns true if singleton for the class is still enabled, which is the default
     * behaviour
     * @return bool
     */
    public function isSingletonEnabled(): bool
    {
        return $this->singletonEnabled;
    }

    /** @inheritDoc */
    public function disableSingleton(): Contract\Injector
    {
        $this->singletonEnabled = false;

        return $this;
    }
}
