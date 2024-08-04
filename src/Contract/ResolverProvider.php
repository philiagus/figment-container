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

use Philiagus\Figment\Container\Contract\Instance\InstanceResolver;
use Philiagus\Figment\Container\Contract\List\ListResolver;

/**
 * A class impelementing this interface can provide resolvers of both lists and instances
 */
interface ResolverProvider
{

    /**
     * Returns a list resolver that has been exposed/redirected with the targeted name or
     * throws an exception if no such name exists
     *
     * @param string $name
     * @return ListResolver
     * @throws \OutOfBoundsException
     */
    public function listResolver(string $name): ListResolver;

    /**
     * Returns an instance resolver that has been exposed/redirected with the targeted name or
     * throws an exception if no such name exists
     *
     * @param string $name
     * @return InstanceResolver
     */
    public function instanceResolver(string $name): InstanceResolver;
}

