<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as HttpResponseAlias;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->renderable(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return Response::api($e->errors(), $e->status);
            }
        });

        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                $statusCode = $e->getCode() > 0 ? $e->getCode() : $e->getStatusCode();
                $statusCode = $statusCode > 0 ? $statusCode : HttpResponseAlias::HTTP_INTERNAL_SERVER_ERROR;

                return Response::api($e->getMessage(), $statusCode);
            }
        });
    }
}
