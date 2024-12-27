<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Builder;

use Philiagus\Figment\Container\Builder\FactoryBuilder;
use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Enum\SingletonMode;
use Philiagus\Figment\Container\Exception\ContainerException;
use Philiagus\Figment\Container\Exception\ContainerRecursionException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

#[CoversClass(FactoryBuilder::class)]
class FactoryBuilderTest extends TestCase
{
    use ProphecyTrait;

    #[TestWith([true], 'Factory by string')]
    #[TestWith([false], 'Factory by instance')]
    public function testBuild(bool $factoryByString): void
    {
        $name = 'theName';
        $builtObject1 = new \stdClass();
        $builtObject2 = new \stdClass();
        $factory = $this->prophesize(Contract\Factory::class);
        $factory
            ->create(Argument::type(Contract\Container::class), $name)
            ->shouldBeCalledTimes(2)
            ->willReturn($builtObject1, $builtObject2);
        $factory->getSingletonMode($name)->willReturn(SingletonMode::DISABLED);
        $factory = $factory->reveal();

        $factoryParameter = $factoryByString ? 'factory' : $factory;

        $container = $this->prophesize(Contract\Container::class);
        if ($factoryByString) {
            $container->get('factory')
                ->shouldBeCalledOnce()
                ->willReturn($factory);
        }
        $container = $container->reveal();

        $config = $this->prophesize(Contract\Configuration::class);
        $config->getContainer()->willReturn($container);
        $config->register(
            Argument::that(
                function (object $o) use (&$instance) {
                    return $o === $instance;
                }
            ),
            'id1', 'id2'
        )->shouldBeCalledOnce();
        $config = $config->reveal();

        $instance = new FactoryBuilder($config, $factoryParameter);
        self::assertSame([$instance], iterator_to_array($instance));
        $instance->registerAs('id1', 'id2');
        self::assertSame($builtObject1, $instance->build($name));
        self::assertSame($builtObject2, $instance->build($name));
    }

    public function testBuild_Error_FactoryIsNoFactory(): void
    {

        $container = $this->prophesize(Contract\Container::class);
        $container->get('factory')
            ->willReturn(new \stdClass());
        $container = $container->reveal();

        $config = $this->prophesize(Contract\Configuration::class);
        $config->getContainer()->willReturn($container);
        $config = $config->reveal();

        $instance = new FactoryBuilder($config, 'factory');
        $this->expectException(ContainerException::class);
        $instance->build('name');
    }

    public function testBuild_Error_FactoryCouldNotBeInstantiated(): void
    {

        $container = $this->prophesize(Contract\Container::class);
        $container->get('factory')
            ->willThrow(new ContainerException());
        $container = $container->reveal();

        $config = $this->prophesize(Contract\Configuration::class);
        $config->getContainer()->willReturn($container);
        $config = $config->reveal();

        $instance = new FactoryBuilder($config, 'factory');
        $this->expectException(ContainerException::class);
        $instance->build('name');
    }

    public function testBuild_Error_RecursionProtection(): void
    {

        $container = $this->prophesize(Contract\Container::class);
        $container = $container->reveal();

        $config = $this->prophesize(Contract\Configuration::class);
        $config->getContainer()->willReturn($container);
        $config = $config->reveal();

        $factory = $this->prophesize(Contract\Factory::class);
        $factory->create($container, 'obj1')->will(
            function () use (&$instance) {
                $instance->build('obj2');
            }
        )->shouldBeCalledOnce();
        $factory->create($container, 'obj2')->will(
            function () use (&$instance) {
                $instance->build('obj1');
            }
        )->shouldBeCalledOnce();
        $factory->getSingletonMode(Argument::any())
            ->willReturn(SingletonMode::DISABLED);
        $factory = $factory->reveal();


        $instance = new FactoryBuilder($config, $factory);
        $this->expectException(ContainerRecursionException::class);
        $instance->build('obj1');
    }
}
