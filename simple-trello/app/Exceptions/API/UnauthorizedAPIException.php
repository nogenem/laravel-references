<?php

namespace App\Exceptions\API;

class UnauthorizedAPIException extends APIException
{
    public const CODE = "UNAUTHORIZED_EXCEPTION";

    public function __construct(array $headers = [])
    {
        parent::__construct(
            403,
            __('exception.unauthorized'),
            $headers
        );
    }
}
