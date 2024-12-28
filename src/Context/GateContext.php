<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Context;

use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Exception\UndefinedContextException;

readonly class GateContext implements Contract\Context
{

    /**
     * This context will allow access to any names listed in $allowedNames _or_
     * matched by $allowedRegex - one of the two is enough
     *
     * @param Contract\Context $source
     * Source context to request the information from if the gat allows the name
     * @param string[] $allowedNames
     * @param null|non-empty-string $allowedRegex
     * Regular expression used to determine allowed names in preg-dialect
     *
     * @see preg_match()
     */
    public function __construct(
        private Contract\Context $source,
        private array $allowedNames = [],
        private null|string $allowedRegex = null
    )
    {

    }

    /** @inheritDoc */
    #[\Override]
    public function has(string $name): bool
    {
        return $this->isAllowed($name) && $this->source->has($name);
    }

    private function isAllowed(string $name): bool
    {
        if (in_array($name, $this->allowedNames, true)) {
            return true;
        }
        if (preg_match($this->allowedRegex, $name)) {
            return true;
        }
        return false;
    }

    /** @inheritDoc */
    #[\Override]
    public function get(string $name): mixed
    {
        if (!$this->isAllowed($name)) {
            throw new UndefinedContextException($name);
        }

        return $this->source->get($name);
    }
}
