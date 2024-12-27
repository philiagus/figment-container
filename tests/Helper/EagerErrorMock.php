<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Helper;

use Philiagus\Figment\Container\Attribute\EagerInstantiation;

#[EagerInstantiation]
class EagerErrorMock
{

    public function __construct()
    {
        throw new \Exception('NOPE!');
    }

}
