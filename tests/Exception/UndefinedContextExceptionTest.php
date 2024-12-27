<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Exception;

use Philiagus\Figment\Container\Exception\UndefinedContextException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(UndefinedContextException::class)]
class UndefinedContextExceptionTest extends TestCase
{

    public function testPrependContainerTrace()
    {
        $exception = new UndefinedContextException('my.id');
        $expectedMessage = 'xyz -> '. $exception->getMessage();
        $this->expectException(UndefinedContextException::class);
        $this->expectExceptionMessage($expectedMessage);
        $exception->prependContainerTrace('xyz');
    }
}
