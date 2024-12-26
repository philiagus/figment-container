<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Context;

use Philiagus\Figment\Container\Context\EmptyContext;
use Philiagus\Figment\Container\Exception\UndefinedContextException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(EmptyContext::class)]
class EmptyContextTest extends TestCase
{
    public function testFull() {
        $context = new EmptyContext();
        self::assertFalse($context->has('anything'));
        $this->expectException(UndefinedContextException::class);
        $context->get('anything');
    }
}
