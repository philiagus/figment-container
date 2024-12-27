<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Integration\AllInjections;

use Philiagus\Figment\Container\Attribute\ContainerId;
use Philiagus\Figment\Container\Attribute\EagerInstantiation;
use Philiagus\Figment\Container\Attribute\Singleton;
use Philiagus\Figment\Container\Enum\SingletonMode;

#[EagerInstantiation]
#[Singleton(SingletonMode::DISABLED)]
readonly class InfoDTO
{
    public function __construct(
        #[ContainerId] public string $id,
        public ?string $info = null,
    )
    {
    }
}
