<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Context;

use Philiagus\Figment\Container\Context\MappingContext;
use Philiagus\Figment\Container\Contract\Context;
use Philiagus\Figment\Container\Exception\UndefinedContextException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

#[CoversClass(MappingContext::class)]
class MappingContextTest extends TestCase
{
    use ProphecyTrait;

    public function testConstructorTypeCheck(): void
    {
        $context = $this->prophesize(Context::class)->reveal();
        $this->expectException(\InvalidArgumentException::class);
        new MappingContext(
            $context,
            [
                'ha' => false
            ]
        );
    }

    public function testMappingSuccess(): void
    {
        $obj = new \stdClass();
        $context = $this->prophesize(Context::class);
        $context->has('mapped')->willReturn(true);
        $context->has('dead')->willReturn(false);
        $context->get('mapped')->willReturn($obj);
        $context = $context->reveal();

        $mapping = new MappingContext(
            $context,
            ['name' => 'mapped']
        );
        self::assertTrue($mapping->has('name'));
        self::assertSame($obj, $mapping->get('name'));

        $mapping = new MappingContext(
            $context,
            [],
            allowUnmappedNames: true
        );
        self::assertTrue($mapping->has('mapped'));
        self::assertSame($obj, $mapping->get('mapped'));

        $mapping = new MappingContext(
            $context,
            [
                'mapped' => 'dead'
            ],
            fallbackToUnmapped: true
        );
        self::assertTrue($mapping->has('mapped'));
        self::assertSame($obj, $mapping->get('mapped'));
    }

    public function testNotFoundAfterMapping(): void
    {
        $context = $this->prophesize(Context::class);
        $context->has('mapped')->willReturn(false);
        $context = $context->reveal();

        $mapping = new MappingContext(
            $context,
            ['name' => 'mapped']
        );
        self::assertFalse($mapping->has('name'));
        $this->expectExceptionObject(new UndefinedContextException('name'));
        $mapping->get('name');
    }

    public function testNotFoundAfterMappingWithFallback(): void
    {
        $context = $this->prophesize(Context::class);
        $context->has('mapped')->willReturn(false);
        $context->has('name')->willReturn(false);
        $context->get('name')->willThrow(
            new UndefinedContextException('name')
        );
        $context = $context->reveal();

        $mapping = new MappingContext(
            $context,
            ['name' => 'mapped'],
            fallbackToUnmapped: true
        );
        self::assertFalse($mapping->has('name'));
        $this->expectExceptionObject(new UndefinedContextException('name'));
        $mapping->get('name');
    }

    public function testNotFoundUnmapped(): void
    {
        $context = $this->prophesize(Context::class);
        $context = $context->reveal();

        $mapping = new MappingContext(
            $context,
            []
        );
        self::assertFalse($mapping->has('name'));
        $this->expectExceptionObject(new UndefinedContextException('name'));
        $mapping->get('name');
    }

    public function testNotFoundPassThroughUnknown(): void
    {
        $context = $this->prophesize(Context::class);
        $context->has('name')->willReturn(false);
        $context->get('name')->willThrow(
            new UndefinedContextException('name')
        );
        $context = $context->reveal();

        $mapping = new MappingContext(
            $context,
            [],
            allowUnmappedNames: true
        );
        self::assertFalse($mapping->has('name'));
        $this->expectExceptionObject(new UndefinedContextException('name'));
        $mapping->get('name');
    }
}
