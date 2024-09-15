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

namespace Philiagus\Figment\Container;

use Psr\Container\NotFoundExceptionInterface;

/**
 * Exception thrown when an id is requested from the container which is
 * not registered/exposed
 *
 *  This class should never be targeted when building catch blocks.
 *  Always catch the corresponding interface
 *
 * @see NotFoundExceptionInterface
 * @internal
 */
class NotFoundException
    extends \OutOfBoundsException
    implements NotFoundExceptionInterface
{
    public function __construct(string $id, ?\Throwable $previous = null)
    {
        parent::__construct("No service of id '$id' is exposed", previous: $previous);
    }
}
