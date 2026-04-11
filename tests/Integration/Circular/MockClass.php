<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Integration\Circular;

use Philiagus\Figment\Container\Attribute\EagerInstantiation;
use Philiagus\Figment\Container\Attribute\Inject;

#[EagerInstantiation]
class MockClass
{

    public function __construct(
        #[Inject('child')] object $child
    )
    {

    }

}
