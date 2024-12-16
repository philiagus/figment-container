<?php
/*
 * This file is part of philiagus/figment-container
 *  
 * (c) Andreas Eicher <philiagus@philiagus.de>
 *
 * For the full copyright and license information, please view the LICENSE 
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Integration;

use Philiagus\Figment\Container\Attribute\Context;
use Philiagus\Figment\Container\Attribute\DisableSingleton;
use Philiagus\Figment\Container\Attribute\Inject;
use Philiagus\Figment\Container\Configuration;
use Philiagus\Figment\Container\Contract\Injectable;
use Philiagus\Figment\Container\Contract\Injector;
use Philiagus\Figment\Container\Contract\List\InstanceList;

require_once __DIR__ . '/../../vendor/autoload.php';

// #[DisableSingleton]
class TestA
{
    private array $args;

    #[Inject('test')]
    public self $self;
    #[Context('name')]
    private string $c;
    #[Context('blubb')]
    private string $d;

    #[Inject('me')]
    private object $me;

    public function __construct(
        ...$args
    )
    {
        $this->args = $args;
    }
}

echo PHP_EOL;
echo '==============================', PHP_EOL;
echo PHP_EOL;

$config = new Configuration(
    ['name' => 'jules']
);

$config
    ->class(TestA::class)
    ->constructorArguments('Hallo')
    ->setContext(['blubb' => 'blübb'], true)
    ->registerAs('test');

$config
    ->generator(
        true,
        function() {
            return (object)['yes' => 'no'];
        }
    )
    ->registerAs('me');

$container = $config->buildContainer();
$start = hrtime(true);
/** @var TestA $object */
$object = $container->get('container')->get('test');
$object->self;
print_r($object);
echo ((hrtime(true) - $start) / 1000000) . 'ms';
