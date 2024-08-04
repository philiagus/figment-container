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
use Philiagus\Parser\Parser\AssertString;

require_once __DIR__ . '/../../vendor/autoload.php';

foreach (glob(__DIR__ . '/../../src/**/*.php') as $file) {
    try {
        include_once $file;
    } catch (\Throwable) {
    }
}
AssertString::new()
    ->parse(Subject::default('a'));

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
            ->instance('self', $this->b)
            ->instance('std1', $this->std1)
            ->instance('std2', $this->std2, true)
            ->instance('container', $this->container)
            ->list('list', $this->list);
        for ($x = 0; $x < 5000; $x++) {
            $injector->parseContext(
                'string',
                AssertString::new()->thenAssignTo($this->hallo)
            );
        }
    }

    public function babbabui(): void
    {
        echo PHP_EOL, PHP_EOL, PHP_EOL, PHP_EOL;
        echo "BABABUI!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!", PHP_EOL;
        foreach ($this->list as $index => $instance) {
            echo $index;
            var_dump($instance);
        }
        var_dump($this->hallo);
    }

}

$config = new Configuration();

$config
    ->instanceGenerator(
        static fn(Injector $injector) => new \stdClass()
    )
    ->exposeAs('std1')
    ->exposeAs('std2');

$config
    ->instanceClass(TestA::class)
    ->setContext([
        'string' => 'Dies ist ein Context'
    ])
    ->redirectList(
        'list',
        $config
            ->list(
                $config->exposedInstance('std1'),
                $config->exposedInstance('std2'),
                $config->instanceObject((object)['baba' => 'bui']),
                $config
                    ->list($config->instanceObject((object)['the last child']))
            )
    )
    ->exposeAs('self');

$container = new Container($config, 'container');
echo "Container creation: ", microtime(true) - $start, PHP_EOL;
$now = microtime(true);
$instance = $container->instance('self');
echo "Resolve: ", microtime(true) - $now, PHP_EOL;
$instance->babbabui();

echo "Full: ", microtime(true) - $start, PHP_EOL;
