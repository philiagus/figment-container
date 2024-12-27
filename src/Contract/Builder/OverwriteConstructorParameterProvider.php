<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Contract\Builder;

use Philiagus\Figment\Container\Builder\OverwriteConstructorParameterBase;
use Philiagus\Figment\Container\Contract\BuilderContainer;
use Philiagus\Figment\Container\Contract\ContainerTraceException;
use Philiagus\Figment\Container\Exception\ContainerException;
use Philiagus\Figment\Container\Exception\ContainerRecursionException;
use Philiagus\Figment\Container\Exception\NotFoundException;
use Philiagus\Figment\Container\Exception\UndefinedContextException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

interface OverwriteConstructorParameterProvider extends BuilderContainer
{
    /**
     * Evaluates the defined list of set parameters as defined by the
     * OverwriteConstructorParameterReceiver. The keys of the return array must
     * be the parameter names.
     *
     * @param string $forId
     *
     * @return array<non-empty-string, mixed>
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ContainerTraceException
     * @throws ContainerException
     * @throws ContainerRecursionException
     * @throws NotFoundException
     * @throws UndefinedContextException
     *
     * @see OverwriteConstructorParameterReceiver
     */
    public function resolveOverwriteConstructorParameter(string $forId): array;
}
