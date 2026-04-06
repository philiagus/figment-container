<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Contract\Override;

use Philiagus\Figment\Container\Contract\Context;

/**
 * @internal
 */
interface OverridableContext
{
    /**
     * Changes the context used to instantiate objects from this configuration to the desired context
     *
     * If $enableFallback is set the system will use the next-higher context if the provided context
     * does not contain a desired field
     *
     * @param Context $context
     * @param bool $enableFallback
     * @return $this
     */
    public function setContext(Context $context, bool $enableFallback = false): static;

    /**
     * Returns the context to be used for creation of new instances
     *
     * @return Context
     */
    public function context(): Context;
}
