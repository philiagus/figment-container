<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test;

use Philiagus\Figment\Container\EmptyInstanceList;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(EmptyInstanceList::class)]
class EmptyInstanceListTest extends TestCase
{
    public function testFull(): void
    {
        $list = new EmptyInstanceList();
        self::assertSame(0, $list->count());
        self::assertEmpty([...$list]);
        self::assertEmpty([...$list->traverseInstances()]);
        self::assertEmpty([...$list->traverseBuilders()]);
        self::assertEmpty([...$list->traverseInstances('unknown class')]);
        self::assertEmpty([...$list->traverseBuilders('unknown class')]);
    }
}
