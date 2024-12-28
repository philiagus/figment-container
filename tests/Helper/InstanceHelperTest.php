<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Helper;

use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Exception\ContainerException;
use Philiagus\Figment\Container\Helper\InstanceHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

#[CoversClass(InstanceHelper::class)]
class InstanceHelperTest extends TestCase
{
    use ProphecyTrait;

    public function testNotInstantiable(): void
    {
        $className = AbstractMock::class;
        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage("Class $className is not instantiable");
        new InstanceHelper($className);
    }

    public function testReflectionError(): void
    {
        $className = 'NOT A CLASS';
        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage("Exception while trying to inspect class $className");
        new InstanceHelper($className);
    }

    public function testEagerErrorInjected(): void
    {
        $className = EagerErrorMock::class;
        $id = 'id';

        $provider = $this->prophesize(Contract\Builder\OverwriteConstructorParameterProvider::class)->reveal();

        $helper = new InstanceHelper($className);
        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage(
            "New instance of class $className " .
            "for id '$id' could not be created"
        );
        $helper->buildInjected($provider, 'id');
    }

    public function testEagerErrorConstructed(): void
    {
        $className = EagerErrorMock::class;
        $id = 'id';

        $provider = $this->prophesize(Contract\Builder\OverwriteConstructorParameterProvider::class)->reveal();

        $helper = new InstanceHelper($className);
        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage(
            "New instance of class $className " .
            "for id '$id' could not be created"
        );
        $helper->buildConstructed($provider, 'id');
    }

    public function testNoParameterValueError(): void
    {
        $className = NoParameterValueMock::class;
        $id = 'id';

        $container = $this->prophesize(Contract\Container::class)->reveal();
        $provider = $this->prophesize(Contract\Builder\OverwriteConstructorParameterProvider::class);
        $provider->getContainer()->willReturn($container);
        $provider->resolveOverwriteConstructorParameter($id)->willReturn([]);
        $provider = $provider->reveal();

        $helper = new InstanceHelper($className);
        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage(
            "Could not create parameter value for not-optional constructor parameter 'value' of '$id'"
        );
        $helper->buildInjected($provider, 'id');
    }
}
