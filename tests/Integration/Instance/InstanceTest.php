<?php

namespace Philiagus\Figment\Container\Test\Integration\Instance;

use Philiagus\Figment\Container\Configuration;
use Philiagus\Figment\Container\Contract\Container;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class InstanceTest extends TestCase
{

    public function testInstance(): void
    {
        $o1 = new \stdClass();
        $o2 = new \stdClass();
        $o3 = new \stdClass();
        $o4 = new \stdClass();

        $config = new Configuration();
        $config->object($o1)->registerAs('1');
        $config->object($o2)->registerAs('2');

        /** @var Representer $inGenerate */
        $inGenerate = $config
            ->attributed(Representer::class)
            ->parameterGenerate(
                'test',
                static function (Container $container) use ($o1) {
                    return $container->instance(
                        Representer::class,
                        ['o1' => $o1, 'o2' => $o1, 'o3' => $o1, 'o4' => $o1]
                    );
                }
            )
            ->build('test');
        $inGenerate->assert(
            $o1, $o2,
            [
                'test' => static function($object) use ($o1) {
                    Assert::assertInstanceOf(Representer::class, $object);
                    $object->assert(
                        $o1, $o1, ['o3' => $o1, 'o4' => $o1]
                    );
                }
            ]
        );

        $container = $config->getContainer();

        $container
            ->instance(Representer::class)
            ->assert($o1, $o2, []);

        $container
            ->instance(
                Representer::class,
                ['o1' => $o3, 'test' => $o4]
            )
            ->assert($o3, $o2, ['test' => $o4]);
    }

}
