<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Integration\AllInjections;

use Philiagus\Figment\Container;
use Philiagus\Figment\Container\Contract;
use PHPUnit\Framework\Assert;

$config = new Container\Configuration(
    new Container\Context\ArrayLookupContext([
        'values' => [
            'yes' => true,
            'no' => false,
            'null' => null
        ],
        'other' => 'default other'
    ])
);

$config
    ->injected(InjectedFull::class)
    ->setContext(new Container\Context\MapContext([
        'other' => 'other'
    ]), true)
    ->redirect('redirectMe', 'injected.default')
    ->registerAs('injected.default');

$config
    ->injected(InjectedFull::class)
    ->setContext(
        new Container\Context\MapContext(['values' => ['altered context']])
    )
    ->setContext(
        new Container\Context\MapContext(['other' => 'altered other']), true
    )
    ->registerAs('injected.context-redirected');

$config
    ->injected(InjectedFull::class)
    ->redirect('does exist by redirection', 'injected.overwritten')
    ->setContext(new Container\Context\MapContext(['context.id' => 'set id']))
    ->parameterSet('context', ['set context'])
    ->parameterSet('otherContext', 'set other')
    ->parameterSet('selfByClass', new InjectedFullChild('class'))
    ->parameterSet('selfById', new InjectedFullChild('id'))
    ->parameterContext('id', 'context.id')
    ->parameterGenerate('list', static function (Contract\Container $c) {
        Assert::assertTrue($c->has('injected.overwritten'));
        Assert::assertFalse($c->has('does not exist'));
        Assert::assertTrue($c->has('does exist by redirection'));
        return new Container\InstantiatedInstanceList(
            $c->get('does exist by redirection')
        );
    })
    ->parameterInject('selfByRedirect', 'injected.overwritten')
    ->registerAs('injected.overwritten');

$config
    ->list()
    ->append(
        InfoDTO::class,
        $config->injected(InfoDTO::class)
            ->parameterSet('info', 'INJECTED'),
        $config->object(new InfoDTO('NoId', 'OBJECT')),
        $config->constructed(InfoDTO::class)
            ->parameterId('id')
            ->parameterSet('info', 'CONSTRUCTED'),
        $config->closure(
            fn(Contract\Container $container, string $id) => new InfoDTO($id, 'CLOSURE')
        )
            ->singletonMode(Container\Enum\SingletonMode::DISABLED),
        $config->factory(new Factory())
    )
    ->registerAs('protoList');

$config->list('list')
    ->append(
        ...$config->list('protoList')
    )
    ->merge(
        $config->list('protoList')
    );

return $config;
