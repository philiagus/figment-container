<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Attribute;

use Philiagus\Figment\Container\Attribute\ContainerId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Philiagus\Figment\Container\Contract;

#[CoversClass(ContainerId::class)]
class ContainerIdTest extends TestCase
{
    use ProphecyTrait;
    public function testResolve(): void
    {
        $instance = new ContainerId();

        $container = $this->prophesize(Contract\Container::class)->reveal();
        $reflectionParameter = new \ReflectionParameter(fn($a) => null, 'a');
        $id = 'my id';

        $hasValue = false;
        self::assertSame(
            $id,
            $instance->resolve($container, $reflectionParameter, $id, $hasValue)
        );
        self::assertTrue($hasValue);
    }
}
