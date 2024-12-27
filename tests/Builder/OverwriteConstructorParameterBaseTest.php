<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Builder;

use Philiagus\Figment\Container\Builder\OverwriteConstructorParameterBase;
use Philiagus\Figment\Container\Context\EmptyContext;
use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Exception\ContainerConfigurationException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

#[CoversClass(OverwriteConstructorParameterBase::class)]
class OverwriteConstructorParameterBaseTest extends TestCase
{
    use ProphecyTrait;

    #[TestWith([''], 'empty string')]
    #[TestWith(['123'], 'numeric string')]
    public function testParameterPatternError(string $name): void
    {
        $config = $this->prophesize(Contract\Configuration::class)->reveal();
        $instance = new OverwriteConstructorParameterBaseMock($config);
        $this->expectException(ContainerConfigurationException::class);
        $this->expectExceptionMessage(
            "The parameter '$name' does not match the requested pattern. " .
            "Parameter names must be provided as name, not index and" .
            " must not be empty"
        );

        $instance->parameterSet($name, true);
    }

    public function testParameterCollisionError(): void
    {
        $config = $this->prophesize(Contract\Configuration::class)->reveal();
        $instance = new OverwriteConstructorParameterBaseMock($config);
        $name = 'test';
        $instance->parameterSet($name, true);
        $this->expectException(ContainerConfigurationException::class);
        $this->expectExceptionMessage(
            "Trying to overwrite parameter '$name' twice"
        );
        $instance->parameterContext($name, 'name');
    }

    public function testConfigurationMapping(): void
    {
        $object = new \stdClass();

        $builder = $this->prophesize(Contract\Builder::class);
        $builder->build('id')->willReturn($object);
        $builder = $builder->reveal();

        $context = new EmptyContext();

        $config = $this->prophesize(Contract\Configuration::class);
        $config->has('id')->willReturn(true);
        $config->has(Argument::any())->willReturn(false);
        $config->get('id')->willReturn($builder);
        $config->context()->willReturn($context);
        $config = $config->reveal();

        $instance = new OverwriteConstructorParameterBaseMock($config);
        self::assertTrue($instance->has('id'));
        self::assertFalse($instance->has('anythingElse'));
        self::assertSame($builder, $instance->get('id'));
        self::assertSame($context, $instance->context());

        $container = $instance->getContainer();
        self::assertTrue($container->has('id'));
        self::assertFalse($container->has('anythingElse'));
        self::assertSame($object, $container->get('id'));
        self::assertSame($context, $container->context());
    }
}
