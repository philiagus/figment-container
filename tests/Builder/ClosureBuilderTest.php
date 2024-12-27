<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Builder;

use Philiagus\Figment\Container\Builder\ClosureBuilder;
use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Enum\SingletonMode;
use Philiagus\Figment\Container\Exception\ContainerRecursionException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

#[CoversClass(ClosureBuilder::class)]
class ClosureBuilderTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @param bool $singleton
     *
     * @return void
     * @throws NotFoundExceptionInterface
     */
    #[TestWith([false], 'No singleton')]
    #[TestWith([true], 'Singleton')]
    public function testFull(bool $singleton)
    {
        $resultObject = new \stdClass();
        $context = $this->prophesize(Contract\Context::class)->reveal();

        $subBuilderObject = new \stdClass();
        $subBuilder = $this->prophesize(Contract\Builder::class);
        $subBuilder->build('name')->shouldBeCalledTimes($singleton ? 1 : 2)->willReturn($subBuilderObject);
        $subBuilder = $subBuilder->reveal();

        $configuration = $this->prophesize(Contract\Configuration::class);
        $configuration->has('name')->shouldBeCalledTimes($singleton ? 1 : 2)->willReturn(true);
        $configuration->get('name')->shouldBeCalledTimes($singleton ? 1 : 2)->willReturn($subBuilder);
        $configuration->context()->shouldBeCalledTimes($singleton ? 1 : 2)->willReturn($context);
        /** @noinspection PhpParamsInspection */
        $configuration->register(
            Argument::that(
                function (object $a) use (&$instance) {
                    return $a === $instance;
                }
            ),
            'id1', 'id2'
        )
            ->shouldBeCalled()
            ->willReturn($configuration);
        $configuration = $configuration->reveal();

        $instance = new ClosureBuilder(
            $configuration,
            function (...$args) use ($subBuilderObject, $context, $resultObject): object {
                self::assertInstanceOf(Contract\Container::class, $args[0]);
                self::assertSame('requested', $args[1]);
                self::assertTrue($args[0]->has('name'));
                self::assertSame($subBuilderObject, $args[0]->get('name'));
                self::assertSame($context, $args[0]->context());
                return (object)['a' => $resultObject];
            }
        );
        if (!$singleton) {
            $instance->singletonMode(SingletonMode::DISABLED);
        }
        self::assertSame([$instance], iterator_to_array($instance));
        $instance->registerAs('id1', 'id2');
        $result1 = $instance->build('requested');
        $result2 = $instance->build('requested');
        self::assertSame($resultObject, $result1->a);
        // call singleton
        self::assertSame($resultObject, $result2->a);
        if ($singleton) {
            self::assertSame($result1, $result2);
        } else {
            self::assertNotSame($result1, $result2);
        }
    }

    public function testRecursionPrevention(): void
    {
        $config = $this->prophesize(Contract\Configuration::class);
        $config->get(Argument::any())->will(
            function () use (&$instance) {
                return $instance;
            }
        );
        $config = $config->reveal();
        $instance = new ClosureBuilder(
            $config,
            function (Contract\Container $container, string $name) {
                return $container->get(
                    match ($name) {
                        'a' => 'b',
                        'b' => 'c',
                        'c' => 'a'
                    }
                );
            }
        );
        $this->expectException(ContainerRecursionException::class);
        $instance->build('a');
    }

    public function testErrorOnNonObjectResult(): void
    {
        $config = $this->prophesize(Contract\Configuration::class);
        $config = $config->reveal();
        $instance = new ClosureBuilder(
            $config,
            fn() => 'string'
        );
        $this->expectException(ContainerExceptionInterface::class);
        $instance->build('a');
    }
}
