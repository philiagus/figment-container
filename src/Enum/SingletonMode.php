<?php
declare(strict_types=1);

namespace Philiagus\Figment\Container\Enum;

use Philiagus\Figment\Container\Exception\ContainerException;

enum SingletonMode
{

    case DISABLED;
    case BY_BUILDER;
    case BY_ID;


    public function resolve(string $forId): null|true|string
    {
        if ($forId === "\x00\xFF") {
            throw new ContainerException(
                "Trying to define singleton behaviour for id that is identical " .
                "with the magical 'singleton on builder level' constant"
            );
        }
        return match ($this) {
            self::DISABLED => null,
            self::BY_BUILDER => "\x00\xFF",
            self::BY_ID => $forId
        };
    }

}
