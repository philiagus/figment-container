<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Exception;

use Philiagus\Figment\Container\Contract\ContainerTraceException;
use Psr\Container\ContainerExceptionInterface;

/**
 * Exception thrown when the resolution of a container leads to a full recursion
 *
 * @iternal
 */
class ContainerRecursionException
    extends \LogicException
    implements ContainerExceptionInterface, ContainerTraceException
{

    public function __construct(string $id)
    {
        parent::__construct(
            "$id: Creation of instance caused attempt at recursive instantiation"
        );
    }

    public function prependContainerTrace(string $traceElement): never
    {
        $this->message = "$traceElement -> $this->message";
        throw $this;
    }

}
