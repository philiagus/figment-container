<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Context;

use Philiagus\Figment\Container\Context\GateContext;
use Philiagus\Figment\Container\Contract\Context;
use Philiagus\Figment\Container\Exception\UndefinedContextException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

#[CoversClass(GateContext::class)]
class GateContextTest extends TestCase
{
    use ProphecyTrait;

    public function testFull(): void
    {
        $object = new \stdClass();

        $context = $this->prophesize(Context::class);
        $context->has('allowed.exists')->willReturn(true);
        $context->has('allowed.missing')->willReturn(false);
        $context->get('allowed.exists')->willReturn($object);
        $context->get('allowed.missing')->willThrow(
            $exception = new UndefinedContextException('allowed.missing')
        );
        $context = $context->reveal();

        $gate = new GateContext(
            $context,
            ['allowed.exists',],
            '~^allowed\.[a-z]++$~'
        );
        self::assertFalse($gate->has('not allowed'));
        self::assertTrue($gate->has('allowed.exists'));
        self::assertFalse($gate->has('allowed.missing'));
        self::assertSame($object, $gate->get('allowed.exists'));
        $caught = null;
        try {
            $gate->get('allowed.missing');
        } catch (\Throwable $caught) {
        }
        self::assertSame($exception, $caught);
        $this->expectException(UndefinedContextException::class);
        $gate->get('not allowed');
    }

}
