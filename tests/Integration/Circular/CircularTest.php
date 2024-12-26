<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Integration\Circular;

use Philiagus\Figment\Container\Exception\ContainerRecursionException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Philiagus\Figment\Container\Contract;

class CircularTest extends TestCase {

    public static function provideCases(): \Generator
    {
        $list = ['a', 'b', 'c', 'd', 'e', 'f'];

        foreach($list as $index => $element) {
            $left = array_slice($list, 0, $index);
            $right = array_slice($list, $index);
            $string = implode(' -> ', [...$right, ...$left, $element]);
            yield [$element, $string];
        }

    }

    #[DataProvider('provideCases')]
    public function test(string $startPoint, string $expectedPath)
    {
        /** @var Contract\Configuration $config */
        $config = require __DIR__ . '/config.php';
        $container = $config->getContainer();
        self::assertTrue($container->has($startPoint));
        $this->expectException(ContainerRecursionException::class);
        $this->expectExceptionMessage("Creation of instance caused attempt at recursive instantiation: $expectedPath");
        $container->get($startPoint);
    }

}
