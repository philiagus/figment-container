<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Enum;

use Philiagus\Figment\Container\Enum\SingletonMode;
use PHPUnit\Framework\TestCase;

class SingletonModeTest extends TestCase
{

    public function testResolve(): void
    {
        self::assertSame(null, SingletonMode::DISABLED->resolve('anything'));
        self::assertSame("\0anything", SingletonMode::BY_ID->resolve('anything'));
        self::assertSame("\0", SingletonMode::BY_BUILDER->resolve('anything'));
    }
}
