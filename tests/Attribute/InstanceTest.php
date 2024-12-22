<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Attribute;

use Philiagus\Figment\Container\Attribute\Instance;
use Philiagus\Figment\Container\Contract\Container;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

#[CoversClass(Instance::class)]
class InstanceTest extends TestCase
{
    use ProphecyTrait;

    public static function provideCases(): \Generator
    {
        yield 'has instance' => [new \stdClass()];
        yield 'no instance' => [null];
    }

    #[DataProvider('provideCases')]
    public function testResolve(?object $instance)
    {
        $hasInstance  = $instance !== null;
        $container = $this->prophesize(Container::class);
        $container->has('targetId')->willReturn($hasInstance);
        $container->get('targetId')->willReturn($instance);
        $container = $container->reveal();
        $parameter = $this->prophesize(\ReflectionParameter::class)->reveal();
        $hasValue = false;

        $inject = new Instance('targetId');
        $result = $inject->resolve($container, $parameter, $hasValue);
        self::assertSame($instance, $result);
        self::assertSame($hasInstance, $hasValue);
    }
}
