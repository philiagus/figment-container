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

namespace Philiagus\Figment\Container\Attribute;

use Philiagus\Figment\Container\Contract\Container;
use Philiagus\Figment\Container\Contract\InjectionAttribute;
use Philiagus\Figment\Container\Contract\BuilderContainer;
use ReflectionParameter;
use ReflectionProperty;


#[\Attribute(\Attribute::TARGET_PARAMETER)]
readonly class Inject implements InjectionAttribute
{

    public function __construct(
        private ?string $id = null
    )
    {
    }

    public function resolve(Container $container, \ReflectionParameter $parameter, bool &$hasValue): object
    {
        $id = $this->id ?? (string)$parameter->getType();
        $instance = $container->get($id);
        $hasValue = true;
        return $instance;
    }
}
