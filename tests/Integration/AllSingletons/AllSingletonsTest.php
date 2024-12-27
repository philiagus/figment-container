<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Integration\AllSingletons;

use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Enum\SingletonMode;
use PHPUnit\Framework\TestCase;

class AllSingletonsTest extends TestCase
{

    public function test(): void
    {
        /** @var Contract\Configuration $config */
        $config = require __DIR__ . '/config.php';
        $container = $config->getContainer();

        $cases = [
            'injected.by-id' => SingletonMode::BY_ID,
            'injected.by-builder' => SingletonMode::BY_BUILDER,
            'injected.disabled' => SingletonMode::DISABLED,

            'constructed.by-id' => SingletonMode::BY_ID,
            'constructed.by-builder' => SingletonMode::BY_BUILDER,
            'constructed.disabled' => SingletonMode::DISABLED,

            'constructed.set.disabled' => SingletonMode::DISABLED,
            'constructed.set.by-id' => SingletonMode::BY_ID,
            'constructed.set.by-builder' => SingletonMode::BY_BUILDER,

            'factory.disabled' => SingletonMode::DISABLED,
            'factory.by-id' => SingletonMode::BY_ID,
            'factory.by-builder' => SingletonMode::BY_BUILDER,

            'get-factory.disabled' => SingletonMode::DISABLED,
            'get-factory.by-id' => SingletonMode::BY_ID,
            'get-factory.by-builder' => SingletonMode::BY_BUILDER,
        ];
        foreach ($cases as $idPrefix => $type) {
            $instances = [];
            for ($i = 0; $i < 4; $i++) {
                $instances[0][] = $container->get("$idPrefix.$i");
                $instances[1][] = $container->get("$idPrefix.$i");
            }
            foreach ($instances[0] as $index => $i0) {
                $i1 = $instances[1][$index];
                if ($type === SingletonMode::BY_BUILDER || $type === SingletonMode::BY_ID) {
                    self::assertSame($i0, $i1);
                } else {
                    self::assertNotSame($i0, $i1);
                }
            }

            foreach ($instances as $instanceList) {
                foreach ($instanceList as $index1 => $instance1) {
                    foreach ($instanceList as $index2 => $instance2) {
                        if ($index1 === $index2) continue;
                        if ($type === SingletonMode::BY_BUILDER) {
                            self::assertSame($instance1, $instance2);
                        } else {
                            self::assertNotSame($instance1, $instance2);
                        }
                    }
                }
            }
        }
    }

}
