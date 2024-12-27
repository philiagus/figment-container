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

namespace Philiagus\Figment\Container\Exception;

use Philiagus\Figment\Container\Contract\ContainerTraceException;
use Psr\Container\ContainerExceptionInterface;

/**
 * Implementation of the ContainerExceptionInterface used by PSR
 *
 * This class should never be targeted when building catch blocks.
 * Always catch the corresponding interface
 *
 * @see ContainerExceptionInterface
 * @internal
 */
class ContainerException
    extends \LogicException
    implements ContainerExceptionInterface, ContainerTraceException
{

    public function prependContainerTrace(string $traceElement): never
    {
        $this->message = "$traceElement -> $this->message";
        throw $this;
    }
}
