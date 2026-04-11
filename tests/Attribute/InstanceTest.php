<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Attribute;

use Philiagus\Figment\Container\Attribute\Inject;
use Philiagus\Figment\Container\Contract\Container;
use Philiagus\Figment\Container\Exception\NotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

#[CoversClass(Inject::class)]
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
        $hasInstance = $instance !== null;
        $container = $this->prophesize(Container::class);
        if (!$hasInstance) {
            $container->get('targetId')->willThrow(new NotFoundException('targetId'));
        } else {
            $container->get('targetId')->willReturn($instance);
        }
        $container = $container->reveal();
        $parameter = $this->prophesize(\ReflectionParameter::class)->reveal();
        $hasValue = false;

        $inject = new Inject('targetId');
        $result = $inject->resolve($container, $parameter, 'id', $hasValue);
        self::assertSame($instance, $result);
        self::assertSame($hasInstance, $hasValue);
    }
}
