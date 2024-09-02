<?php
namespace App\Exceptions;

use App\Helpers\SendResponse;
use App\Enums\StatutEnum;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use League\OAuth2\Server\Exception\OAuthServerException; // Ajout de l'import nécessaire
use Illuminate\Http\JsonResponse;

use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function report(Throwable $exception): void
    {
        // Kill reporting if this is an "access denied" (code 9) OAuthServerException.
        if ($exception instanceof OAuthServerException && $exception->getCode() == 9) {
            return;
        }

        parent::report($exception);
    }

    public function render($request, Throwable $exception): JsonResponse
    {
        // Handle JWT related exceptions
        if ($exception instanceof JWTException) {
            return SendResponse::jsonResponse(
                null,
                401,
                StatutEnum::FAILURE,
                'JWT error: ' . $exception->getMessage()
            );
        }

        // Handle validation exceptions
        if ($exception instanceof ValidationException) {
            return SendResponse::jsonResponse(
                $exception->errors(),
                422,
                StatutEnum::FAILURE,
                'Validation error'
            );
        }

        // Handle model not found exceptions
        if ($exception instanceof ModelNotFoundException) {
            return SendResponse::jsonResponse(
                null,
                404,
                StatutEnum::FAILURE,
                'Resource not found'
            );
        }

        // Handle not found HTTP exceptions
        if ($exception instanceof NotFoundHttpException) {
            return SendResponse::jsonResponse(
                null,
                404,
                StatutEnum::FAILURE,
                'Resource not found'
            );
        }

        // Handle method not allowed HTTP exceptions
        if ($exception instanceof MethodNotAllowedHttpException) {
            return SendResponse::jsonResponse(
                null,
                405,
                StatutEnum::FAILURE,
                'Method not allowed'
            );
        }
        
        // Handle other exceptions
        return SendResponse::jsonResponse(
            null,
            500,
            StatutEnum::FAILURE,
            'An unexpected error occurred'
        );
    }
}
