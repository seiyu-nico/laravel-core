<?php

namespace SeiyuNico\LaravelCore\Exceptions;

use Throwable;

class S3DeleteFailException extends S3Exception
{
    public function __construct(string $path = '', ?Throwable $previous = null)
    {
        parent::__construct("ファイルの削除に失敗しました。 File: {$path}", 0, $previous);
    }
}
