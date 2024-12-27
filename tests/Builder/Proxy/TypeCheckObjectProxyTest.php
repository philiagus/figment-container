<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Builder\Proxy;

use Philiagus\Figment\Container\Builder\Proxy\TypeCheckObjectProxy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TypeCheckObjectProxy::class)]
class TypeCheckObjectProxyTest extends TestCase
{
    public function testGetIterator(): void
    {
        $object = new \stdClass();
        $proxy = new TypeCheckObjectProxy($object, null);
        self::assertSame([$proxy], iterator_to_array($proxy));
    }
}
