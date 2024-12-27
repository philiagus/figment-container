<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Builder;

use Philiagus\Figment\Container\Builder\InjectionBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Philiagus\Figment\Container\Contract;

#[CoversClass(InjectionBuilder::class)]
class InjectionBuilderTest extends TestCase
{
    use ProphecyTrait;

    public function testGetIterator(): void
    {
        $config = $this->prophesize(Contract\Configuration::class)->reveal();
        $helper = $this->prophesize(Contract\Helper\HelperProvider::class)->reveal();
        $className = self::class;
        $instance = new InjectionBuilder(
            $config,
            $helper,
            $className
        );

        self::assertSame([$instance], iterator_to_array($instance));
    }
}
