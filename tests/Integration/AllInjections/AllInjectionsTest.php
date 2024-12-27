<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Integration\AllInjections;

use Philiagus\Figment\Container\Contract;
use PHPUnit\Framework\TestCase;

class AllInjectionsTest extends TestCase
{

    public function test()
    {
        /** @var Contract\Configuration $config */
        $config = require __DIR__ . '/config.php';

        $container = $config->getContainer();
        /** @var InjectedFull $obj */
        $obj = $container->get('injected.default');
        self::assertSame(
            [
                'yes' => true,
                'no' => false,
                'null' => null
            ],
            $obj->context
        );

        self::assertSame('injected.default', $obj->id);
        self::assertSame($obj, $obj->selfById);
        self::assertSame(InjectedFull::class, $obj->selfByClass->id);

        $this->assertList($obj->list);
        $this->assertList($obj->list);
        $this->assertOverwritten($container);
        $this->assertContextRedirected($container);
    }

    private function assertContextRedirected(Contract\Container $container): void
    {
        /** @var InjectedFull $obj */
        $obj = $container->get('injected.context-redirected');
        self::assertSame(['altered context'], $obj->context);
        self::assertSame('altered other', $obj->otherContext);
        self::assertSame('injected.context-redirected', $obj->id);
    }


    private function assertOverwritten(Contract\Container $container): void
    {
        /** @var InjectedFull $obj */
        $obj = $container->get('injected.overwritten');
        self::assertSame(['set context'], $obj->context);
        self::assertSame('set other', $obj->otherContext);

        $selfByClass = $obj->selfByClass;
        self::assertInstanceOf(InjectedFullChild::class, $selfByClass);
        self::assertSame('class', $selfByClass->info);

        $selfById = $obj->selfById;
        self::assertInstanceOf(InjectedFullChild::class, $selfById);
        self::assertSame('id', $selfById->info);

        self::assertSame('set id', $obj->id);

        self::assertCount(1, $obj->list);

        self::assertSame($obj, $obj->selfByRedirect);


        $objects = iterator_to_array($obj->list);
        self::assertSame([$obj], $objects);
        foreach(
            [
                null,
                InjectedFull::class,
                [\stdClass::class, InjectedFull::class],
                static fn(object $o) => $o instanceof InjectedFull
            ] as $type
        ) {
            $objects = [];
            foreach($obj->list->traverseBuilders($type) as $builder) {
                $objects[] = $builder->build('does not matter');
            }
            self::assertSame([$obj], $objects);

            $objects = iterator_to_array(
                $obj->list->traverseInstances($type)
            );
            self::assertSame([$obj], $objects);
        }
    }

    private function assertList(Contract\InstanceList $list): void
    {
        $expectedListContent = [
            [InfoDTO::class, null],
            'INJECTED',
            ['NoId', 'OBJECT'],
            'CONSTRUCTED',
            'CLOSURE',
            'FACTORY',
            [InfoDTO::class, null],
            'INJECTED',
            ['NoId', 'OBJECT'],
            'CONSTRUCTED',
            'CLOSURE',
            'FACTORY',
        ];
        self::assertSame(count($expectedListContent), $list->count());
        self::assertSame(count($list), $list->count());

        /**
         * @var int $index
         * @var InfoDTO $expectedInfo
         */
        foreach($list as $index => $listContent) {
            $expectedInfo = $expectedListContent[$index];
            if(is_array($expectedInfo)) {
                [$expectedId, $expectedInfo] = $expectedInfo;
            } else {
                $expectedId = "list#$index";
            }
            self::assertSame($expectedId, $listContent->id);
            self::assertSame($expectedInfo, $listContent->info, "Mismatch for index $index");
        }

        self::assertEquals(
            iterator_to_array($list->traverseInstances()),
            iterator_to_array($list->traverseInstances(InfoDTO::class))
        );
    }

}
