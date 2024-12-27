<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Integration\AllInjections;

readonly class InjectedFullChild extends InjectedFull
{

    /** @noinspection PhpMissingParentConstructorInspection */
    public function __construct(
        public readonly string $info
    )
    {
    }

}
