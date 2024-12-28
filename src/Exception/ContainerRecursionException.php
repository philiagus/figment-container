<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Exception;

use Philiagus\Figment\Container\Contract\PrependMessageThrowableInterface;
use Psr\Container\ContainerExceptionInterface;

/**
 * Exception thrown when the resolution of a container leads to a full recursion
 *
 * @iternal
 */
class ContainerRecursionException
    extends \LogicException
    implements ContainerExceptionInterface, PrependMessageThrowableInterface
{

    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        parent::__construct(
            "$id: Creation of instance caused attempt at recursive instantiation"
        );
    }

    /** @inheritDoc */
    #[\Override]
    public function prependMessage(string $traceElement, string $glue = ' -> '): never
    {
        $this->message = $traceElement . $glue . $this->message;
        throw $this;
    }

}
