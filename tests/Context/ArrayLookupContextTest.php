<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Context;

use Philiagus\Figment\Container\Context\ArrayLookupContext;
use Philiagus\Figment\Container\Exception\UndefinedContextException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ArrayLookupContext::class)]
class ArrayLookupContextTest extends TestCase
{

    public function testFull()
    {
        $content = [
            '1' => [
                '1' => [
                    '1' => 'element 1.1.1',
                    '2' => 'element 1.1.2'
                ],
                '2' => [
                    '1' => 'element 1.2.1',
                    '2' => 'element 1.2.2'
                ]
            ],
            '2' => [
                '1' => [
                    '1' => 'element 2.1.1',
                    '2' => 'element 2.1.2'
                ],
                '2' => [
                    '1' => 'element 2.2.1',
                    '2' => 'element 2.2.2'
                ]
            ],
        ];
        $context = new ArrayLookupContext($content);

        $paths = [
            '1.1.1' => 'element 1.1.1',
            '1.1.2' => 'element 1.1.2',
            '1.2.1' => 'element 1.2.1',
            '1.2.2' => 'element 1.2.2',
            '2.1.1' => 'element 2.1.1',
            '2.1.2' => 'element 2.1.2',
            '2.2.1' => 'element 2.2.1',
            '2.2.2' => 'element 2.2.2'
        ];
        foreach ($paths as $path => $expectedContent) {
            self::assertTrue($context->has($path));
            self::assertSame($expectedContent, $context->get($path));
        }

        $paths = [
            '1|1|1' => 'element 1.1.1',
            '1|1|2' => 'element 1.1.2',
            '1|2|1' => 'element 1.2.1',
            '1|2|2' => 'element 1.2.2',
            '2|1|1' => 'element 2.1.1',
            '2|1|2' => 'element 2.1.2',
            '2|2|1' => 'element 2.2.1',
            '2|2|2' => 'element 2.2.2'
        ];
        $context = new ArrayLookupContext($content, '|');
        foreach ($paths as $path => $expectedContent) {
            self::assertTrue($context->has($path));
            self::assertSame($expectedContent, $context->get($path));
        }

        self::assertFalse($context->has('not there'));
        $this->expectException(UndefinedContextException::class);
        $context->get('not there');
    }
}
