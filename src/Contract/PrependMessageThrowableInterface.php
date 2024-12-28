<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Contract;

interface PrependMessageThrowableInterface extends \Throwable
{

    /**
     * Prepends the provided trace element to the exception message and throws
     * the current exception class
     *
     * This is used to build up information chains by continuously prepending
     * ids and info while the exception is moving up the call chain, passing
     * various builders.
     *
     * @param string $traceElement String to prepend to the exception message
     * @param string $glue A glue string used between the $traceElement and the
     *                     existing exception message
     *
     * @return never
     * @throws static
     */
    public function prependMessage(string $traceElement, string $glue = ' -> '): never;

}
