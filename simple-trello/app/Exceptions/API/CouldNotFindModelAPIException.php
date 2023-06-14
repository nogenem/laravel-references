<?php

namespace App\Exceptions\API;

class CouldNotFindModelAPIException extends APIException
{
    public const CODE = "COULD_NOT_FIND_MODEL_EXCEPTION";

    public function __construct(string $model, array $headers = [])
    {
        parent::__construct(
            404,
            __('exception.could_not_find_model', ['model' => $model]),
            $headers
        );
    }
}
