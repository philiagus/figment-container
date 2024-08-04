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

use Philiagus\Figment\Container\Instance\AbstractInstanceConfigurator;
use Philiagus\Parser\Base\Subject;
use Philiagus\Parser\Contract\Parser;

class Injector implements Contract\Injector
{

    private const int TYPE_INSTANCE = 1,
        TYPE_LIST = 2,
        TYPE_CONTEXT = 3,
        TYPE_CONTEXT_PARSE = 4;

    /** @var array<array{"0":int,"1":string,"2":mixed,"3":bool}> */
    private array $injections = [];

    public function __construct(
        private readonly AbstractInstanceConfigurator $source
    )
    {
    }

    public function context(string $name, mixed &$target): Contract\Injector
    {
        $this->injections[] = [self::TYPE_CONTEXT, $name, &$target, null];

        return $this;
    }

    public function parseContext(string $name, Parser $parser): Contract\Injector
    {
        $this->injections[] = [self::TYPE_CONTEXT_PARSE, $name, $parser, null];

        return $this;
    }

    public function execute(): void
    {
        foreach ($this->injections as [$type, $name, &$target, $etc]) {
            if ($type === self::TYPE_CONTEXT_PARSE) {
                /** @var Parser $target */
                $target->parse(Subject::default($this->source->getContext($name), "Context '$name'"));
            } else {
                $target = match ($type) {
                    self::TYPE_INSTANCE => $this->source->instance($name, $etc),
                    self::TYPE_LIST => $this->source->list($name),
                    self::TYPE_CONTEXT => $this->source->getContext($name)
                };
            }
        }
    }

    public function instance(string $name, mixed &$target, bool $disableSingleton = false): Contract\Injector
    {
        $this->injections[] = [self::TYPE_INSTANCE, $name, &$target, $disableSingleton];

        return $this;
    }

    public function list(string $name, mixed &$target): Contract\Injector
    {
        $this->injections[] = [self::TYPE_LIST, $name, &$target, null];

        return $this;
    }
}
