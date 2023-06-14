<?php

namespace App\Exceptions\API;

class CouldNotDeleteModelAPIException extends APIException
{
    public const CODE = "COULD_NOT_DELETE_MODEL_EXCEPTION";

    public function __construct(string $model, array $headers = [])
    {
        parent::__construct(
            500,
            __('exception.could_not_delete_model', ['model' => $model]),
            $headers
        );
    }
}
