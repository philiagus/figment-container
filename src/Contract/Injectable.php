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

/**
 * If a class wants to be instantiated by the container it must
 * implement this interface.
 *
 * The interface defines the constructor of the class, thus ensuring that
 * the constructor can always be called by the framework.
 *
 * @see Injector
 */
interface Injectable
{

    /**
     * The constructor of any Injectable class is provided with an Injector.
     *
     * The Injector is used to request any dependencies from within the class.
     *
     * The injections of the injector are done _after_ the constructor has been resolved
     * and the instance of the object has been instantiated. This means that you can
     * use _none_ of the injected elements in the constructor and can only use them in
     * later method calls of the class.
     *
     * This is done to ensure that no invalid circular dependency state can be achieved.
     *
     * @param Injector $injector
     */
    public function __construct(Injector $injector);

}
