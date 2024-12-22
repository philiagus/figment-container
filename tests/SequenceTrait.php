<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test;

use PHPUnit\Framework\Attributes\DataProvider;

trait SequenceTrait {

    abstract public static function provideTestcases(): array|\Generator;

    #[DataProvider('provideTestcases')]
    public function testSequences(\Closure ...$sequence): void
    {
        $baseInjections = $this->prepareInjectables();
        foreach($sequence as $step) {
            $reflection = new \ReflectionFunction($step);
            $parameterNames = array_map(
                fn(\ReflectionParameter $p) =>$p->name,
                $reflection->getParameters()
            );
            $step(...array_intersect_key(
                $baseInjections,
                array_flip($parameterNames),
            ));
        }
    }


    /**
     * @return array<string, object>
     */
    abstract protected function prepareInjectables(): array;

}
