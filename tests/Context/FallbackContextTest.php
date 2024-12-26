<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Context;

use Philiagus\Figment\Container\Context\FallbackContext;
use Philiagus\Figment\Container\Contract\Context;
use Philiagus\Figment\Container\Exception\UndefinedContextException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

#[CoversClass(FallbackContext::class)]
class FallbackContextTest extends TestCase
{
    use ProphecyTrait;

    public function testFull() {
        $result =new \stdClass();

        $context1 = $this->prophesize(Context::class);
        $context1->has(Argument::any())->willReturn(false);
        $context1->get(Argument::any())->shouldNotBeCalled();
        $context1 = $context1->reveal();

        $context2 = $this->prophesize(Context::class);
        $context2->has('field1')->willReturn(true);
        $context2->get('field1')->willReturn($result);
        $context2->has(Argument::any())->willReturn(false);
        $context2 = $context2->reveal();

        $fallback = new FallbackContext($context1, $context2);
        self::assertTrue($fallback->has('field1'));
        self::assertFalse($fallback->has('field2'));

        self::assertSame($result, $fallback->get('field1'));
        $this->expectException(UndefinedContextException::class);
        $fallback->get('field2');
    }
}
