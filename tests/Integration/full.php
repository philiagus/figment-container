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

require_once __DIR__ . '/../../vendor/autoload.php';

class TestA implements Injectable
{

    private ?self $b;
    private ?\stdClass $std1;
    private ?\stdClass $std2;

    private ?InstanceList $list;

    private ?Container $container;

    private ?RumsBums $rumsBums;

    private string $hallo = '';
    private string $defaultContext = '';

    public function __construct(Injector $injector)
    {
        $injector
            ->inject('self', $this->b)
            ->inject('std1', $this->std1)
            ->inject('std2', $this->std2)
            ->inject('list', $this->list)
            ->inject('container', $this->container)
            ->inject(RumsBums::class, $this->rumsBums)
            ->context('msg', $this->hallo)
            ->context('default.config', $this->defaultContext);
    }

    public function babbabui(): void
    {
        echo PHP_EOL, PHP_EOL, PHP_EOL, PHP_EOL;
        var_dump($this->rumsBums);
        foreach ($this->list as $index => $instance) {
            echo $index, ' --> ';
            var_dump($instance);
        }
    }

}

class RumsBums implements Injectable
{
    public function __construct(Injector $injector)
    {
    }
}

for ($loops = 0; $loops < 2; $loops++) {
    echo PHP_EOL;
    echo '==============================', PHP_EOL;
    echo PHP_EOL;
    $start = microtime(true);

    $config = new Configuration(['default.config' => 'yes, this is default']);
    $config->list(
        $config->object((object)['the last child'])
    )->exposeAs('list');

    $config
        ->class(TestA::class)
        ->context(['msg' => 'Dies ist ein Context'], true)
        ->redirect(
            'list',
            $config
                ->list(
                    $config->get('std1'),
                    $config->get('std2'),
                    $config->object((object)['baba' => 'bui']),
                    ...$config->get('list')
                )
        )
        ->exposeAs('self');

    $config
        ->generator(
            static function (Injector $injector) {
                $injector->disableSingleton();
                return new \stdClass();
            }
        )
        ->exposeAs('std1', 'std2');

    $container = new Container($config, 'container');
    echo "Container creation: ", number_format(microtime(true) - $start, 5, '.', ''), PHP_EOL;
    $now = microtime(true);
    $instance = $container->get('self');
    echo "Resolve: ", number_format(microtime(true) - $now, 5, '.', ''), PHP_EOL;
    
    echo "Full: ", microtime(true) - $start, PHP_EOL;
}
