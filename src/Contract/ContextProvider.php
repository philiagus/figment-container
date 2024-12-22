<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Contract;

interface ContextProvider {
    /**
     * @return Context
     */
    public function context(): Context;
}
