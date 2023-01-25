<?php

namespace SeiyuNico\LaravelCore\Exceptions;

use Exception;
use Throwable;

class LaravelCoreException extends Exception
{
    public function __construct(string $message = '', int $code = 422, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
