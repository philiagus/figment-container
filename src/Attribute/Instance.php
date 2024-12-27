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

use Philiagus\Figment\Container\Contract\Configuration;
use Philiagus\Figment\Container\Contract\Container;
use Philiagus\Figment\Container\Contract\InjectionAttribute;
use Philiagus\Figment\Container\Exception\NotFoundException;


/**
 * Used as attribute on constructor parameters this injection type is used by
 * the Configuration::inject method of instance creation to target a registered
 * service for injection. The result of this injection will always be an object
 *
 * @see Configuration::injected()
 */
#[\Attribute(\Attribute::TARGET_PARAMETER)]
readonly class Instance implements InjectionAttribute
{

    /**
     * @param null|string $id
     */
    public function __construct(private ?string $id = null)
    {
    }

    /** @inheritDoc */
    public function resolve(
        Container            $container,
        \ReflectionParameter $parameter,
        string               $id,
        false                &$hasValue
    ): ?object
    {
        $targetId = $this->id ?? (string)$parameter->getType();
        try {
            $instance = $container->get($targetId);
        } catch (NotFoundException) {
            return null;
        }
        $hasValue = true;
        return $instance;
    }
}
