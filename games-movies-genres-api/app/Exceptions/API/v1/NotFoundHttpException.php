<?php

namespace App\Exceptions\API\v1;

use Symfony\Component\HttpKernel\Exception\HttpException;

class NotFoundHttpException extends HttpException
{
    public function __construct(string $message = null, array $headers = [])
    {
        parent::__construct(
            404,
            $message ?? __('http.not_found'),
            null,
            $headers
        );
    }
}
