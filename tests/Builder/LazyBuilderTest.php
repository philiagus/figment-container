<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Builder;

use Philiagus\Figment\Container\Builder\LazyBuilder;
use Philiagus\Figment\Container\Exception\NotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Philiagus\Figment\Container\Contract;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\NotFoundExceptionInterface;

#[CoversClass(LazyBuilder::class)]
class LazyBuilderTest extends TestCase
{
    use ProphecyTrait;
    public function testById(): void
    {
        $lazyId = 'lazyId';
        $targetId= 'targetId';
        $resultObject = new \stdClass();

        $builder = $this->prophesize(Contract\Builder::class);
        $builder->build($targetId)
            ->shouldBeCalledTimes(2)
            ->willReturn($resultObject);
        $builder = $builder->reveal();

        $config = $this->prophesize(Contract\Configuration::class);
        $config->has($lazyId)->willReturn(true);
        $config->get($lazyId)->willReturn($builder);
        $config = $config->reveal();

        $instance = new LazyBuilder($config, $lazyId);
        self::assertSame([$builder], iterator_to_array($instance));
        self::assertSame($resultObject, $instance->build($targetId));
        self::assertSame($resultObject, $instance->build($targetId));
    }
    public function testByClass(): void
    {
        $lazyId = \stdClass::class;
        $targetId= 'targetId';
        $resultObject = new \stdClass();

        $builder = $this->prophesize(Contract\Builder\InjectionBuilder::class);
        $builder->build($targetId)
            ->shouldBeCalledTimes(2)
            ->willReturn($resultObject);
        $builder->registerAs($lazyId)
            ->shouldBeCalledOnce();
        $builder = $builder->reveal();

        $config = $this->prophesize(Contract\Configuration::class);
        $config->has($lazyId)->willReturn(false);
        $config->injected($lazyId)->willReturn($builder);
        $config = $config->reveal();

        $instance = new LazyBuilder($config, $lazyId);
        self::assertSame([$builder], iterator_to_array($instance));
        self::assertSame($resultObject, $instance->build($targetId));
        self::assertSame($resultObject, $instance->build($targetId));
    }
    public function testNotFoundBuild(): void
    {
        $lazyId = 'not a class';
        $targetId= 'targetId';
        $resultObject = new \stdClass();

        $config = $this->prophesize(Contract\Configuration::class);
        $config->has($lazyId)->willReturn(false);
        $config = $config->reveal();

        $instance = new LazyBuilder($config, $lazyId);
        $this->expectException(NotFoundException::class);
        $instance->build($targetId);
    }
    public function testNotFoundIterator(): void
    {
        $lazyId = 'not a class';
        $targetId= 'targetId';
        $resultObject = new \stdClass();

        $config = $this->prophesize(Contract\Configuration::class);
        $config->has($lazyId)->willReturn(false);
        $config = $config->reveal();

        $instance = new LazyBuilder($config, $lazyId);
        $this->expectException(NotFoundException::class);
        iterator_to_array($instance);
    }
}
