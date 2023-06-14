<?php

namespace App\Http\Responses\API;

use Illuminate\Http\JsonResponse;

class JsonExceptionResponse extends JsonResponse
{
    public function __construct(string $message, string $code, $status = 200, $headers = [])
    {
        parent::__construct(['message' => $message, 'code' => $code], $status, $headers);
    }
}
