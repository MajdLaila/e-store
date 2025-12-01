<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
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
     */
    public function render($request, Throwable $e): JsonResponse|\Symfony\Component\HttpFoundation\Response
    {
        // Handle API requests
        if ($request->is('api/*')) {
            return $this->handleApiException($request, $e);
        }

        return parent::render($request, $e);
    }

    /**
     * Handle API exceptions
     */
    protected function handleApiException(Request $request, Throwable $e): JsonResponse
    {
        // Handle validation exceptions
        if ($e instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        // Handle HTTP exceptions
        if ($e instanceof HttpException) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'HTTP Error',
                'error' => config('app.debug') ? $e->getTraceAsString() : null
            ], $e->getStatusCode());
        }

        // Handle custom API exceptions
        if ($e instanceof ApiException) {
            return $e->render($request);
        }

        // Special case: "Route [login] not defined." error
        $errorMessage = config('app.debug') ? $e->getMessage() : null;
        if ($errorMessage === 'Route [login] not defined.') {
            return response()->json([
                'success' => false,
                'message' => 'يرجى إدخال رمز الدخول (التوكن) أو انتهت صلاحية التوكن',
                'error' => ''
            ], 401);
        }

        // Handle other exceptions
        return response()->json([
            'success' => false,
            'message' => 'Internal server error',
            'error' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}
