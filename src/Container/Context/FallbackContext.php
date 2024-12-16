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

namespace Philiagus\Figment\Container\Context;

use Philiagus\Figment\Container\Contract\Context;
use Philiagus\Figment\Container\UndefinedContextException;

readonly class FallbackContext implements Context {
    
    private array $contexts;

    public function __construct(
        Context ...$contexts
    ) {
        $this->contexts = $contexts;
    }

    public function has(string $name): bool
    {
        foreach($this->contexts as $context) {
            if($context->has($name)) {
                return true;
            }
        }
        return false;
    }

    public function get(string $name): mixed
    {
        foreach($this->contexts as $context) {
            if($context->has($name)) {
                return $context->get($name);
            }
        }
        throw new UndefinedContextException($name);
    }
}
