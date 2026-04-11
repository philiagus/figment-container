<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Attribute;

/**
 * Configures the class to be instantiated eagerly
 * By default, figment-container uses lazy instantiation for performance reasons
 * In certain conditions it can be necessary to force the container to
 * create the instance eagerly.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class EagerInstantiation
{
}
