<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiException extends Exception
{
  protected $statusCode;
  protected $errors;

  public function __construct(string $message = 'API Error', int $statusCode = 500, array $errors = [])
  {
    parent::__construct($message);
    $this->statusCode = $statusCode;
    $this->errors = $errors;
  }

  public function getStatusCode(): int
  {
    return $this->statusCode;
  }

  public function getErrors(): array
  {
    return $this->errors;
  }

  /**
   * Render the exception as an HTTP response.
   */
  public function render(Request $request): JsonResponse
  {
    return response()->json([
      'success' => false,
      'message' => $this->getMessage(),
      'errors' => $this->getErrors(),
      'status_code' => $this->getStatusCode(),
      'timestamp' => now()->toIso8601String(),

    ], $this->getStatusCode());
  }
}
