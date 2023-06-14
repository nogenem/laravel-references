<?php

namespace App\Exceptions\API\v1;

use Symfony\Component\HttpKernel\Exception\HttpException;

class CouldNotDeleteModelHttpException extends HttpException
{
    public function __construct(string $model, array $headers = [])
    {
        parent::__construct(
            500,
            __('exception.could_not_delete_model', ['model' => $model]),
            null,
            $headers
        );
    }
}
