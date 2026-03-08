<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Contract\Builder;

use Philiagus\Figment\Container\Contract\Builder;

interface MapBuilder extends Registrable, Builder {

    /**
     * Sets the defined key to be associated with the defined builder
     *  The builder can be provided as instances, created by the configuration
     *  and/or as ids
     *
     * @param mixed $key
     * @param Builder|string $builder
     *
     * @return $this
     */
    public function set(mixed $key, Builder|string $builder): static;

}
