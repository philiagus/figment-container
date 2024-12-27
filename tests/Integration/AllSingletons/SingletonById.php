<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Test\Integration\AllSingletons;

use Philiagus\Figment\Container\Attribute\Singleton;
use Philiagus\Figment\Container\Enum\SingletonMode;

#[Singleton(SingletonMode::BY_ID)]
class SingletonById
{
}
