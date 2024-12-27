<?php
/*
 * This file is part of philiagus/figment-container
 *
 * (c) Andreas Eicher <philiagus@philiagus.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Philiagus\Figment\Container\Attribute;

use Philiagus\Figment\Container\Enum\SingletonMode;

/**
 * Allows to set the default singleton mode used for this class
 * If this attribute is not defined a singleton mode per builder is assumed
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
readonly class Singleton
{

    public function __construct(public SingletonMode $mode)
    {
    }
}
