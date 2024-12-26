<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Builder;

use Philiagus\Figment\Container\Container;
use Philiagus\Figment\Container\Context\FallbackContext;
use Philiagus\Figment\Container\Contract;

abstract class OverwriteConstructorParameterBase
    implements
    Contract\Builder\OverwriteConstructorParameterReceiver,
    Contract\Builder\OverwriteConstructorParameterProvider,
    Contract\Override\OverridableContext
{

    private const int IS_FIXED = 1,
        IS_INJECTED = 2,
        IS_CONFIG = 3,
        IS_GENERATED = 4;

    /** @var array<string, array{int, mixed}> */
    private array $parameters = [];

    private Contract\Context $context;

    public function __construct(
        protected readonly Contract\Configuration $configuration
    )
    {
    }

    public function resolveOverwriteConstructorParameter(string $forName): array
    {
        $realParameters = [];
        $selfAsContainer = $this->getContainer();
        foreach ($this->parameters as $name => [$type, $value]) {
            $realParameters[$name] = match ($type) {
                self::IS_FIXED => $value,
                self::IS_INJECTED => $value instanceof Contract\Builder ? $value->build("$forName parameter $name")
                    : $this->configuration->get($value)->build("$forName parameter $name"),
                self::IS_CONFIG => $this->configuration->context()->get($value),
                self::IS_GENERATED => $value($selfAsContainer, $forName),
            };
        }
        return $realParameters;
    }

    public function get(string $id): Contract\Builder
    {
        return $this->configuration->get($id);
    }

    public function context(): Contract\Context
    {
        return $this->context ?? $this->configuration->context();
    }

    public function parameterSet(string $name, mixed $value): static
    {
        $this->parameters[$name] = [self::IS_FIXED, $value];

        return $this;
    }

    public function parameterInject(string $name, string|Contract\Builder $injection): static
    {
        $this->parameters[$name] = [self::IS_INJECTED, $injection];

        return $this;
    }

    public function parameterContext(string $name, string $context): static
    {
        $this->parameters[$name] = [self::IS_CONFIG, $context];

        return $this;
    }

    public function parameterGenerate(string $name, \Closure $generator): static
    {
        $this->parameters[$name] = [self::IS_GENERATED, $generator];

        return $this;
    }

    public function has(string $id): bool
    {
        return $this->configuration->has($id);
    }

    public function setContext(Contract\Context $context, bool $enableFallback = false): static
    {
        if ($enableFallback) {
            $this->context = new FallbackContext($context, $this->context);
        } else {
            $this->context = $context;
        }

        return $this;
    }

    /**
     * @return Contract\Container
     */
    public function getContainer(): Contract\Container
    {
        return new Container($this);
    }
}
