<?php

namespace SeiyuNico\LaravelCore\Exceptions;

use Throwable;

class S3UploadFailException extends S3Exception
{
    public function __construct(string $path = '', ?Throwable $previous = null)
    {
        parent::__construct("ファイルの追加に失敗しました。 File: {$path}", 0, $previous);
    }
}
