<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Context;

use Philiagus\Figment\Container\Context\MapContext;
use Philiagus\Figment\Container\Exception\UndefinedContextException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MapContext::class)]
class MapContextTest extends TestCase
{

    public function testFull(): void
    {
        $map = new MapContext(
            [
                'some.thing' => 'field 1',
                'another.thing.entirely' => 'field 2'
            ]
        );

        self::assertTrue($map->has('some.thing'));
        self::assertSame('field 1', $map->get('some.thing'));
        self::assertTrue($map->has('another.thing.entirely'));
        self::assertSame('field 2', $map->get('another.thing.entirely'));
        self::assertFalse($map->has('another.thing'));
        $this->expectException(UndefinedContextException::class);
        $map->get('another.thing');
    }

}
