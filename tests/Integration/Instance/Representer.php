<?php

namespace Philiagus\Figment\Container\Test\Integration\Instance;

use Philiagus\Figment\Container\Attribute\Instance;
use PHPUnit\Framework\Assert;

readonly class Representer
{

    public array $parameters;


    public function __construct(
        #[Instance('1')] public object $o1,
        #[Instance('2')] public object $o2,
        ...$parameters
    )
    {
        $this->parameters = $parameters;
    }

    public function assert(
        object $o1,
        object $o2,
        $parameters
    ) {
        Assert::assertSame($o1, $this->o1);
        Assert::assertSame($o2, $this->o2);
        Assert::assertSame(array_keys($parameters), array_keys($this->parameters));
        foreach($parameters as $name => $value) {
            if($value instanceof \Closure) {
                $value($this->parameters[$name]);
            } else {
                Assert::assertSame($value, $this->parameters[$name]);
            }
        }
    }

}
