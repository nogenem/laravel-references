<?php

namespace App\Http\Responses\API\v1;

use Illuminate\Http\JsonResponse;

class JsonMessageResponse extends JsonResponse
{
    public function __construct(string $message, $status = 200, $headers = [])
    {
        parent::__construct(['message' => $message], $status, $headers);
    }

    public static function fromMessage(string $message, $status = 200, $headers = [])
    {
        return new static($message, $status, $headers);
    }
}
