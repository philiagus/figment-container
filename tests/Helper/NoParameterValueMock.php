<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Helper;

use Philiagus\Figment\Container\Attribute\EagerInstantiation;
use Philiagus\Figment\Container\Attribute\Instance;
use Philiagus\Figment\Container\Contract\Container;
use Philiagus\Figment\Container\Contract\InjectionAttribute;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
#[EagerInstantiation]
class NoParameterValueMock implements InjectionAttribute
{

    public function __construct(
        #[NoParameterValueMock(null)] null $value
    ) {

    }

    public function resolve(Container $container, \ReflectionParameter $parameter, string $id, false &$hasValue): mixed
    {
        return null;
    }
}
