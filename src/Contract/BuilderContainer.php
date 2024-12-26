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

namespace Philiagus\Figment\Container\Contract;

use Psr\Container\NotFoundExceptionInterface;

interface BuilderContainer extends ContextProvider
{

    /**
     * Returns the builder registered under the provided name
     *
     * Throws a NotFoundExceptionInterface if the provided ID has
     * not been registered to the configuration
     * @param string $id
     * @return Builder
     * @throws NotFoundExceptionInterface
     */
    public function get(string $id): Builder;

    /**
     * Returns true if a builder has already been registered to the provided id
     *
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool;

    /**
     * Returns a container instance that encapsulates this Configuration
     * In the context of this framework a container is simply a wrapper around
     * a configuration that allows easy access to objects created by the
     * builders within the configuration
     *
     *
     * @return Container
     */
    public function getContainer(): Container;

}
