<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Integration\AllInjections;

use Philiagus\Figment\Container\Attribute\ContainerId;
use Philiagus\Figment\Container\Attribute\DisableSingleton;
use Philiagus\Figment\Container\Attribute\EagerInstantiation;

#[EagerInstantiation]
#[DisableSingleton]
readonly class InfoDTO {
    public function __construct(
        #[ContainerId] public string $id,
        public ?string $info = null,
    ){
    }
}
