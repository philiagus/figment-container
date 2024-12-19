<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Resolver;

use Philiagus\Figment\Container\Context\ArrayContext;
use Philiagus\Figment\Container\Context\FallbackContext;
use Philiagus\Figment\Container\Contract\Configuration;
use Philiagus\Figment\Container\Contract\Configuration\OverwriteConstructorParameterProvider;
use Philiagus\Figment\Container\Contract\Configuration\OverwriteConstructorParameterReceiver;
use Philiagus\Figment\Container\Contract\Provider;
use Philiagus\Figment\Container\Contract\Resolver;
use Philiagus\Figment\Container\Contract;

class OverwriteConstructorParameterBase
    implements OverwriteConstructorParameterReceiver, OverwriteConstructorParameterProvider,
    Provider, Configuration\OverridableContext
{

    private const int IS_FIXED = 1,
        IS_INJECTED = 2,
        IS_CONFIG = 3;

    /** @var array<string, array{int, mixed}> */
    private array $parameters = [];

    private ?Contract\Context $context = null;

    public function __construct(protected readonly Configuration $configuration)
    {
    }

    public function resolveOverwriteConstructorParameter(): array
    {
        $realParameters = [];
        foreach ($this->parameters as $name => [$type, $value]) {
            $realParameters[$name] = match ($type) {
                self::IS_FIXED => $value,
                self::IS_INJECTED => $value instanceof Resolver ? $value
                    : $this->configuration->get($value),
                self::IS_CONFIG => $this->configuration->context()->get($value)
            };
        }
        return $realParameters;
    }

    public function parameterSet(string $name, mixed $value): static
    {
        $this->parameters[$name] = [self::IS_FIXED, $value];

        return $this;
    }

    public function parameterInject(string $name, string|Resolver $injection): static
    {
        $this->parameters[$name] = [self::IS_INJECTED, $injection];

        return $this;
    }

    public function parameterConfig(string $name): static
    {
        $this->parameters[$name] = [self::IS_CONFIG, $name];

        return $this;
    }

    public function has(string $id): bool
    {
        return $this->configuration->has($id);
    }

    public function setContext(Contract\Context|array $context, bool $enableFallback = false): static
    {
        if (!$context instanceof Contract\Context) {
            $context = new ArrayContext($context);
        }

        if ($enableFallback) {
            $this->context = new FallbackContext($context, $this->context);
        } else {
            $this->context = $context;
        }

        return $this;
    }

    public function get(string $id): Resolver
    {
        return $this->configuration->get($id);
    }

    public function context(): Contract\Context
    {
        return $this->context ?? $this->configuration->context();
    }
}
