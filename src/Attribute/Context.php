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

#[\Attribute(\Attribute::TARGET_PARAMETER)]
readonly class Context implements InjectionAttribute
{

    public function __construct(private string $name)
    {
    }

    /** @inheritDoc */
    #[\Override]
    public function resolve(
        Container $container,
        \ReflectionParameter $parameter,
        string $id,
        false &$hasValue
    ): mixed
    {
        $context = $container->context();
        if (!$context->has($this->name)) {
            return null;
        }
        $hasValue = true;
        return $context->get($this->name);
    }
}
