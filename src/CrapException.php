<?php

namespace Geekish\Crap;

use Exception;

/**
 * Class CrapException
 * @package Geekish\Crap
 */
final class CrapException extends Exception
{
    /**
     * @param string $message
     * @param array ...$args
     * @return CrapException
     */
    public static function create($message, ...$args)
    {
        return new self(vsprintf($message, $args));
    }
}
