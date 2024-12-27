<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test;

use Philiagus\Figment\Container\Configuration;
use Philiagus\Figment\Container\Exception\ContainerConfigurationException;
use Philiagus\Figment\Container\Exception\ContainerException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Philiagus\Figment\Container\Contract;

#[CoversClass(Configuration::class)]
class ConfigurationTest extends TestCase
{
    use ProphecyTrait;
    public function testListResultsInNonList(): void
    {
        $builder = $this->prophesize(Contract\Builder::class)->reveal();
        $configuration = new Configuration();
        $configuration->register($builder, 'id');
        $this->expectException(ContainerException::class);
        $configuration->list('id');
    }

    public function testDoubleRegistration(): void
    {
        $builder = $this->prophesize(Contract\Builder::class)->reveal();
        $builder2 = $this->prophesize(Contract\Builder::class)->reveal();
        $configuration = new Configuration();
        $configuration->register($builder, 'id');
        $configuration->register($builder, 'id');
        $this->expectException(ContainerConfigurationException::class);
        $configuration->register($builder2, 'id');
    }
}
