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
 * @see Configuration::attributed()
 */
#[\Attribute(\Attribute::TARGET_PARAMETER)]
readonly class Inject implements InjectionAttribute
{

    /** @var array<string|object> */
    private array $targets;

    /**
     * @param string|object ...$target
     */
    public function __construct(string|object ...$target)
    {
        $this->targets = $target;
    }

    /** @inheritDoc */
    #[\Override]
    public function resolve(
        Container $container,
        \ReflectionParameter $parameter,
        string $id,
        false &$hasValue
    ): ?object
    {
        $targets = $this->targets ?: [(string) $parameter->getType()];
        foreach ($targets as $target) {
            if (is_object($target)) {
                $hasValue = true;

                return $target;
            } else try {
                $instance = $container->get($target);
                $hasValue = true;

                return $instance;
            } catch (NotFoundException) {
            }
        }
        $hasValue = false;

        return null;
    }
}
