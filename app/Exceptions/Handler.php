<?php
namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request, Throwable $exception): JsonResponse
    {
        // Handle JWT related exceptions
        if ($exception instanceof JWTException) {
            return response()->json([
                'status' => 401,
                'message' => 'JWT error: ' . $exception->getMessage(),
                'data' => null,
            ], 401);
        }

        // Handle validation exceptions
        if ($exception instanceof ValidationException) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation error',
                'data' => $exception->errors(),
            ], 422);
        }

        // Handle model not found exceptions
        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'status' => 404,
                'message' => 'Resource not found',
                'data' => null,
            ], 404);
        }

        // Handle not found HTTP exceptions
        if ($exception instanceof NotFoundHttpException) {
            return response()->json([
                'status' => 404,
                'message' => 'Resource not found',
                'data' => null,
            ], 404);
        }

        // Handle method not allowed HTTP exceptions
        if ($exception instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'status' => 405,
                'message' => 'Method not allowed',
                'data' => null,
            ], 405);
        }

        // Handle other exceptions
        return response()->json([
            'status' => 500,
            'message' => 'An unexpected error occurred',
            'data' => null,
        ], 500);
    }
}
