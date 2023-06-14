<?php

namespace App\Exceptions\API\v1;

use Symfony\Component\HttpKernel\Exception\HttpException;

class UnprocessableEntityHttpException extends HttpException
{
    public function __construct(string $message, array $headers = [])
    {
        parent::__construct(
            422,
            $message,
            null,
            $headers
        );
    }
}
