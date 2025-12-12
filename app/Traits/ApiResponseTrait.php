<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    protected function ok(mixed $data = null, string $message = 'Success'): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], 200);
    }

    protected function created(mixed $data = null, string $message = 'Created successfully'): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], 201);
    }

    protected function noContent(): JsonResponse
    {
        return response()->json(null, 204);
    }

    protected function badRequest(string $message = 'Bad request', array $errors = []): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'errors' => $errors,
        ], 400);
    }

    protected function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], 401);
    }

    protected function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], 403);
    }

    protected function notFound(string $message = 'Not found'): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], 404);
    }

    protected function unprocessable(string $message = 'Unprocessable entity', array $errors = []): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'errors' => $errors,
        ], 422);
    }

    protected function serverError(string $message = 'Internal server error'): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], 500);
    }
}
