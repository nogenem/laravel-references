<?php

namespace App\Exceptions\API\v1;

use Symfony\Component\HttpKernel\Exception\HttpException;

class CouldNotUpdateModelHttpException extends HttpException
{
    public function __construct(string $model, array $headers = [])
    {
        parent::__construct(
            500,
            __('exception.could_not_update_model', ['model' => $model]),
            null,
            $headers
        );
    }
}
