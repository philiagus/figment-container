<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Builder\Proxy;

use Philiagus\Figment\Container\Builder\Proxy\TypeCheckProxy;
use Philiagus\Figment\Container\Contract\Builder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerExceptionInterface;

#[CoversClass(TypeCheckProxy::class)]
class TypeCheckProxyTest extends TestCase
{
    use ProphecyTrait;

    public static function provideCases(): \Generator
    {
        $object = new \stdClass();
        yield 'closure match' => [
            $object,
            function (object $o) use ($object) {
                self::assertSame($object, $o);
                return true;
            },
            true
        ];
        yield 'closure no match' => [
            $object,
            function (object $o) use ($object) {
                self::assertSame($object, $o);
                return false;
            },
            false
        ];

        yield 'string match' => [
            $object,
            \stdClass::class,
            true
        ];
        yield 'string no match' => [
            $object,
            self::class,
            false
        ];

        yield 'array match' => [
            $object,
            [self::class, 'something that does not exist', \stdClass::class, 'something else after that'],
            true
        ];
        yield 'array no match' => [
            $object,
            [self::class, 'something that does not exist'],
            false
        ];
    }

    #[DataProvider('provideCases')]
    public function testBuild(
        object                $builderResult,
        \Closure|array|string $type,
        bool                  $expectSuccess
    ): void
    {
        $name = 'name';
        $builder = $this->prophesize(Builder::class);
        $builder->build($name)->shouldBeCalledOnce()->willReturn($builderResult);
        $builder = $builder->reveal();

        $instance = new TypeCheckProxy($builder, $type);
        self::assertSame([$instance], iterator_to_array($instance));
        if (!$expectSuccess) {
            $this->expectException(ContainerExceptionInterface::class);
        }
        $result = $instance->build($name);
        self::assertSame($builderResult, $result);
    }
}
