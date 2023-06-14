<?php

namespace App\Exceptions\API;

use Symfony\Component\HttpKernel\Exception\HttpException;

class APIException extends HttpException
{
    public const CODE = "API_EXCEPTION";

    public function __construct(int $status, string $message, array $headers = [])
    {
        parent::__construct(
            $status,
            $message,
            null,
            $headers
        );
    }
}
