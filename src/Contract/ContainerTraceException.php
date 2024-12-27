<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Contract;

interface ContainerTraceException extends \Throwable
{

    public function prependContainerTrace(string $traceElement): never;

}
