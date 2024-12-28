<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Exception;

use Philiagus\Figment\Container\Exception\ContainerException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ContainerException::class)]
class ContainerExceptionTest extends TestCase
{
    public function testPrependContainerTrace(): void
    {
        $exception = new ContainerException('abc');
        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage('xyz -> abc');
        $exception->prependMessage('xyz');
    }
}
