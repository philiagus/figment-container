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

use Philiagus\Figment\Container\Configuration;
use Philiagus\Figment\Container\Container;
use Philiagus\Figment\Container\Contract\Injectable;
use Philiagus\Figment\Container\Contract\Injector;
use Philiagus\Figment\Container\Contract\List\InstanceList;
use Philiagus\Parser\Base\Subject;
use Philiagus\Parser\Parser\Assert\AssertString;

require_once __DIR__ . '/../../vendor/autoload.php';

foreach (glob(__DIR__ . '/../../src/**/*.php') as $file) {
    try {
        include_once $file;
    } catch (\Throwable) {
    }
}

$start = microtime(true);

class TestA implements Injectable
{

    private ?self $b;
    private ?\stdClass $std1;
    private ?\stdClass $std2;

    private ?InstanceList $list;

    private ?Container $container;

    private string $hallo = '';

    public function __construct(Injector $injector)
    {
        $injector
            ->inject('self', $this->b)
            ->inject('std1', $this->std1)
            ->inject('std2', $this->std2)
            ->inject('list', $this->list)
            ->inject('container', $this->container)
            ->parseContext(
                'msg',
                AssertString::new()->thenAssignTo($this->hallo)
            );
    }

    public function babbabui(): void
    {
        echo PHP_EOL, PHP_EOL, PHP_EOL, PHP_EOL;
        foreach ($this->list as $index => $instance) {
            echo $index, ' --> ';
            var_dump($instance);
        }
    }

}

$config = new Configuration();

$config
    ->class(TestA::class)
    ->context(['msg' => 'Dies ist ein Context'])
    ->redirect(
        'list',
        $config
            ->list(
                $config->get('std1'),
                $config->get('std2'),
                $config->object((object)['baba' => 'bui']),
                ...$config->list($config->object((object)['the last child']))
            )
    )
    ->exposeAs('self');

$config
    ->generator(
        static fn(Injector $injector) => new \stdClass()
    )
    ->exposeAs('std1')
    ->exposeAs('std2');

$container = new Container($config, 'container');
echo "Container creation: ", microtime(true) - $start, PHP_EOL;
$now = microtime(true);
$instance = $container->get('self');
echo "Resolve: ", microtime(true) - $now, PHP_EOL;
$instance->babbabui();

echo "Full: ", microtime(true) - $start, PHP_EOL;
