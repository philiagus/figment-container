<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Builder\Proxy;

use Philiagus\Figment\Container\Builder\Proxy\RedirectionProxy;
use Philiagus\Figment\Container\Contract\Configuration;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

#[CoversClass(RedirectionProxy::class)]
class RedirectionProxyTest extends TestCase
{
    use ProphecyTrait;
    public function testGetIterator(): void
    {
        $config = $this->prophesize(Configuration::class)->reveal();
        $instance = new RedirectionProxy($config, 'target');
        self::assertSame([$instance], iterator_to_array($instance));
    }
}
