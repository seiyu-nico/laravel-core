<?php

namespace SeiyuNico\LaravelCore\Exceptions;

use Throwable;

class NotFoundException extends LaravelCoreException
{
    public function __construct(string $message = '', int $code = 404, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
