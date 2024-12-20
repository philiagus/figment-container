<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Exception;

use Psr\Container\ContainerExceptionInterface;

class ContainerRecursionException
    extends \LogicException
    implements ContainerExceptionInterface
{

    private array $path;

    public function __construct(string ...$path)
    {
        $this->path = $path;
        parent::__construct(
            "Creation of instance caused attempt at recursive instantiation: " . implode(' -> ', $path)
        );
    }

    public function prepend(string $path): never
    {
        throw new self($path, ...$this->path);
    }

}
