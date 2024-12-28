<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Builder;

use Philiagus\Figment\Container\Container;
use Philiagus\Figment\Container\Context\FallbackContext;
use Philiagus\Figment\Container\Contract;
use Philiagus\Figment\Container\Exception\ContainerConfigurationException;

/**
 * @internal
 */
abstract class OverwriteConstructorParameterBase
    implements
    Contract\Builder\OverwriteConstructorParameterReceiver,
    Contract\Builder\OverwriteConstructorParameterProvider,
    Contract\Override\OverridableContext
{

    private const int IS_FIXED = 1,
        IS_INJECTED = 2,
        IS_CONFIG = 3,
        IS_GENERATED = 4,
        IS_ID = 5;

    /** @var array<string, array{int, mixed}> */
    private array $parameters = [];

    private Contract\Context $context;

    /**
     * @param Contract\Configuration $configuration
     */
    public function __construct(
        protected readonly Contract\Configuration $configuration
    )
    {
    }

    /** @inheritDoc */
    #[\Override]
    public function resolveOverwriteConstructorParameter(string $forId): array
    {
        $realParameters = [];
        $selfAsContainer = $this->getContainer();
        foreach ($this->parameters as $name => [$type, $value]) {
            $realParameters[$name] = match ($type) {
                self::IS_FIXED => $value,
                self::IS_INJECTED => new Proxy\RedirectionProxy($this->configuration, $value)
                    ->build("$forId parameter $name"),
                self::IS_CONFIG => $this->context()->get($value),
                self::IS_GENERATED => $value($selfAsContainer, $forId),
                self::IS_ID => $forId,
            };
        }
        return $realParameters;
    }

    /** @inheritDoc */
    #[\Override]
    public function getContainer(): Contract\Container
    {
        return new Container($this);
    }

    /** @inheritDoc */
    #[\Override]
    public function get(string $id): Contract\Builder
    {
        return $this->configuration->get($id);
    }

    /** @inheritDoc */
    #[\Override]
    public function context(): Contract\Context
    {
        return $this->context ?? $this->configuration->context();
    }

    /** @inheritDoc */
    #[\Override]
    public function parameterSet(string $name, mixed $value): static
    {
        return $this->parameter($name, self::IS_FIXED, $value);
    }

    /**
     * @param string $name
     * @param int $type
     * @param mixed|null $definition
     *
     * @return $this
     *
     * @throws ContainerConfigurationException
     */
    private function parameter(
        string $name, int $type, mixed $definition = null
    ): static
    {
        if (empty($name) || preg_match('~^\d++$~', $name)) {
            throw new ContainerConfigurationException(
                "The parameter '$name' does not match the requested pattern. " .
                "Parameter names must be provided as name, not index and" .
                " must not be empty"
            );
        }
        if (isset($this->parameters[$name])) {
            throw new ContainerConfigurationException(
                "Trying to overwrite parameter '$name' twice"
            );
        }
        $this->parameters[$name] = [$type, $definition];

        return $this;
    }

    /** @inheritDoc */
    #[\Override]
    public function parameterInject(string $name, string|Contract\Builder $injection): static
    {
        return $this->parameter($name, self::IS_INJECTED, $injection);
    }

    /** @inheritDoc */
    #[\Override]
    public function parameterContext(string $name, string $contextName): static
    {
        return $this->parameter($name, self::IS_CONFIG, $contextName);
    }

    /** @inheritDoc */
    #[\Override]
    public function parameterId(string $name): static
    {
        return $this->parameter($name, self::IS_ID);
    }

    /** @inheritDoc */
    #[\Override]
    public function parameterGenerate(string $name, \Closure $generator): static
    {
        return $this->parameter($name, self::IS_GENERATED, $generator);
    }

    /** @inheritDoc */
    #[\Override]
    public function has(string $id): bool
    {
        return $this->configuration->has($id);
    }

    /** @inheritDoc */
    #[\Override]
    public function setContext(Contract\Context $context, bool $enableFallback = false): static
    {
        if ($enableFallback) {
            $this->context = new FallbackContext(
                $context,
                $this->context ?? $this->configuration->context()
            );
        } else {
            $this->context = $context;
        }

        return $this;
    }
}
