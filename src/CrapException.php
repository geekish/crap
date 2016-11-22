<?php

namespace Geekish\Crap;

use Exception;

final class CrapException extends Exception
{
    public static function create($message, ...$args)
    {
        return new self(vsprintf($message, $args));
    }
}
