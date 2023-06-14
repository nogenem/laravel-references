<?php

namespace App\Exceptions;

use Throwable;
use App\Exceptions\API\APIException;
use App\Http\Responses\API\JsonExceptionResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (APIException $e) {
            return new JsonExceptionResponse($e->getMessage(), $e::CODE, $e->getStatusCode(), $e->getHeaders());
        });
    }
}
