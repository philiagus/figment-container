<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Exception;

use Philiagus\Figment\Container\Exception\NotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(NotFoundException::class)]
class NotFoundExceptionTest extends TestCase
{

    public function testPrependContainerTrace(): void
    {
        $exception = new NotFoundException('my.id');
        $expectedMessage = 'xyz -> ' . $exception->getMessage();
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage($expectedMessage);
        $exception->prependMessage('xyz');
    }
}
