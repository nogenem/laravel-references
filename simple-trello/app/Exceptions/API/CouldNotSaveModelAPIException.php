<?php

namespace App\Exceptions\API;

class CouldNotSaveModelAPIException extends APIException
{
    public const CODE = "COULD_NOT_SAVE_MODEL_EXCEPTION";

    public function __construct(string $model, array $headers = [])
    {
        parent::__construct(
            500,
            __('exception.could_not_save_model', ['model' => $model]),
            $headers
        );
    }
}
