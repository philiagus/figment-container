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

use Philiagus\Figment\Container\Contract\PrependMessageThrowableInterface;

/**
 * Exception thrown when a context field is requested that cannot be provided
 *
 * @internal
 */
class UndefinedContextException extends \LogicException implements PrependMessageThrowableInterface
{

    /**
     * @param string $contextName
     */
    public function __construct(string $contextName, ?\Throwable $previous = null)
    {
        parent::__construct("The context '$contextName' is not registered", previous: $previous);
    }

    /** @inheritDoc */
    #[\Override]
    public function prependMessage(string $traceElement, string $glue = ' -> '): never
    {
        $this->message = $traceElement . $glue . $this->message;
        throw $this;
    }
}
