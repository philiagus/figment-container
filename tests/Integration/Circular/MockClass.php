<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Integration\Circular;

use Philiagus\Figment\Container\Attribute\DisableSingleton;
use Philiagus\Figment\Container\Attribute\EagerInstantiation;
use Philiagus\Figment\Container\Attribute\Instance;

#[EagerInstantiation]
class MockClass {

    public function __construct(
        #[Instance('child')] object $child
    ) {

    }

}
