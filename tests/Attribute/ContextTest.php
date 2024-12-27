<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Attribute;

use Philiagus\Figment\Container\Attribute\Context;
use Philiagus\Figment\Container\Contract\Container;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

#[CoversClass(Context::class)]
class ContextTest extends TestCase
{
    use ProphecyTrait;

    public static function provideCases(): \Generator
    {
        yield 'has value' => [true, new \stdClass()];
        yield 'no value' => [false, null];
    }

    #[DataProvider('provideCases')]
    public function testResolve(bool $hasContext, mixed $contextValue): void
    {
        $context = $this->prophesize(\Philiagus\Figment\Container\Contract\Context::class);
        $context->has('field')->willReturn($hasContext);
        $context->get('field')->willReturn($contextValue);
        $context = $context->reveal();

        $container = $this->prophesize(Container::class);
        $container->context()->willReturn($context);
        $container = $container->reveal();

        $parameter = $this->prophesize(\ReflectionParameter::class)->reveal();

        $hasValue = false;
        $inject = new Context('field');
        $result = $inject->resolve($container, $parameter, 'id', $hasValue);
        self::assertSame($hasContext, $hasValue);
        self::assertSame($contextValue, $result);
    }
}
