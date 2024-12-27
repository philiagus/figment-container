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

class UndefinedContextException extends \LogicException implements ContainerTraceException
{

    public function __construct(string $contextName)
    {
        parent::__construct("The context '$contextName' is not registered");
    }

    public function prependContainerTrace(string $traceElement): never
    {
        $this->message = "$traceElement -> $this->message";
        throw $this;
    }
}
