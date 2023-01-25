<?php

namespace SeiyuNico\LaravelCore\Exceptions;

use Throwable;

class InvalidParameterException extends LaravelCoreException
{
    public function __construct(string $message = '', int $code = 422, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
