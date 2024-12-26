<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Builder;

use Philiagus\Figment\Container\Builder\ConstructorBuilder;
use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Exception\ContainerRecursionException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

#[CoversClass(ConstructorBuilder::class)]
class ConstructorBuilderTest extends TestCase
{
    use ProphecyTrait;

    #[TestWith([true, false], 'Singleton enabled')]
    #[TestWith([false, false], 'Singleton disabled')]
    #[TestWith([true, true], 'Singleton enabled but disabled by reflection')]
    #[TestWith([false, true], 'Singleton disabled by both')]
    public function testBuild(bool $singleton, bool $singletonDisabledByReflection = false)
    {
        $singletonEvaluatesEnabled = $singleton && !$singletonDisabledByReflection;
        $object1 = new \stdClass();
        $object2 = new \stdClass();
        $configuration = $this->prophesize(Contract\Configuration::class);
        $configuration
            ->register(
                Argument::that(
                    function (object $b) use (&$builder) {
                        return $b === $builder;
                    }
                ),
                'id1', 'id2'
            )
            ->shouldBeCalledOnce()
            ->willReturn($configuration);
        $configuration = $configuration->reveal();

        $helper = $this->prophesize(Contract\Helper\InstanceHelper::class);
        $helper->buildConstructed(
            Argument::that(
                function (object $b) use (&$builder) {
                    return $b === $builder;
                }
            ),
            'test'
        )
            ->shouldBeCalledTimes($singletonEvaluatesEnabled ? 1 : 2)
            ->willReturn($object1, $object2);
        $helper->singletonDisabled = $singletonDisabledByReflection;
        $helper = $helper->reveal();


        $helperProvider = $this->prophesize(Contract\Helper\HelperProvider::class);
        $helperProvider->get('className')->shouldBeCalledTimes($singletonEvaluatesEnabled ? 1 : 2)->willReturn($helper);
        $helperProvider = $helperProvider->reveal();

        $builder = new ConstructorBuilder(
            $configuration,
            $helperProvider,
            'className'
        );
        $builder->registerAs('id1', 'id2');
        self::assertSame([$builder], iterator_to_array($builder));

        if (!$singleton) {
            $builder->disableSingleton();
        }

        self::assertSame($object1, $builder->build('test'));
        if($singleton && !$singletonDisabledByReflection) {
            self::assertSame($object1, $builder->build('test'));
        } else {
            self::assertSame($object2, $builder->build('test'));
        }
    }
    public function testBuildRecursionProtection()
    {
        $configuration = $this->prophesize(Contract\Configuration::class);
        $configuration = $configuration->reveal();

        $helper = $this->prophesize(Contract\Helper\InstanceHelper::class);
        $helper->buildConstructed(
            Argument::that(
                function (object $b) use (&$builder) {
                    return $b === $builder;
                }
            ),
            'test'
        )
            ->will(
                function() use (&$builder) {
                    $builder->build('test2');
                }
            );
        $helper->buildConstructed(
            Argument::that(
                function (object $b) use (&$builder) {
                    return $b === $builder;
                }
            ),
            'test2'
        )
            ->will(
                function() use (&$builder) {
                    $builder->build('test');
                }
            );
        $helper = $helper->reveal();


        $helperProvider = $this->prophesize(Contract\Helper\HelperProvider::class);
        $helperProvider->get('className')->willReturn($helper);
        $helperProvider = $helperProvider->reveal();

        $builder = new ConstructorBuilder(
            $configuration,
            $helperProvider,
            'className'
        );
        $this->expectException(ContainerRecursionException::class);
        $builder->build('test');
    }
}
