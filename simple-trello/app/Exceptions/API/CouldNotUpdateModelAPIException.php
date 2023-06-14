<?php

namespace App\Exceptions\API;

class CouldNotUpdateModelAPIException extends APIException
{
    public const CODE = "COULD_NOT_UPDATE_MODEL_EXCEPTION";

    public function __construct(string $model, array $headers = [])
    {
        parent::__construct(
            500,
            __('exception.could_not_update_model', ['model' => $model]),
            $headers
        );
    }
}
