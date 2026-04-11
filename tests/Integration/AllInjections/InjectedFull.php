<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Integration\AllInjections;

use Philiagus\Figment\Container\Attribute\ContainerId;
use Philiagus\Figment\Container\Attribute\Context;
use Philiagus\Figment\Container\Attribute\Inject;
use Philiagus\Figment\Container\Contract\InstanceList;

readonly class InjectedFull
{

    public function __construct(
        #[Context('values')] public array           $context,
        #[Context('other')] public string           $otherContext,
        #[Inject] public InjectedFull               $selfByClass,
        #[Inject('injected.default')] public object $selfById,
        #[ContainerId] public string                $id,
        #[Inject('list')] public InstanceList       $list,
        #[Inject('redirectMe')] public ?object      $selfByRedirect = null,
    )
    {
    }
}
