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
        $list = ['a', 'child redirected to b', 'b', 'c', 'd', 'child redirected to e', 'e', 'f'];

        foreach($list as $index => $element) {
            if(strlen($element) !== 1) continue;
            $left = array_slice($list, 0, $index);
            $right = array_slice($list, $index);
            yield "Start from $element" => [$element, implode(' -> ', [...$right, ...$left, $element])];
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
        $this->expectExceptionMessage("$expectedPath: Creation of instance caused attempt at recursive instantiation");
        $container->get($startPoint);
    }

}
