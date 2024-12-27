<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Builder;

use Philiagus\Figment\Container\Builder\ObjectBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Philiagus\Figment\Container\Contract;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

#[CoversClass(ObjectBuilder::class)]
class ObjectBuilderTest extends TestCase
{
    use ProphecyTrait;
    public function testFull(): void
    {
        $configuration = $this->prophesize(Contract\Configuration::class);
        $configuration->register(
            Argument::that(
                function(object $o) use (&$builder) {
                    return $o === $builder;
                }
            ),
            'id1', 'id2'
        )->shouldBeCalled();
        $configuration = $configuration->reveal();

        $object = new \stdClass();
        $builder = new ObjectBuilder($configuration, $object);
        $builder->registerAs('id1', 'id2');

        self::assertSame([$builder], iterator_to_array($builder));
        self::assertSame($object, $builder->build('test'));

    }
}
