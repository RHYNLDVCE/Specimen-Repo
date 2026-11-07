<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponseTrait
{
    /**
     * Return a standardized success response.
     */
    protected function respondSuccess(mixed $data = null, string $message = 'Success', int $status = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $status);
    }

    /**
     * Return a standardized created (201) response.
     */
    protected function respondCreated(mixed $data = null, string $message = 'Resource created successfully'): JsonResponse
    {
        return $this->respondSuccess($data, $message, 201);
    }

    /**
     * Return a standardized paginated response.
     */
    protected function respondPaginated(LengthAwarePaginator $paginator, mixed $resourceData, string $message = 'Resources retrieved successfully'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $resourceData,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ], 200);
    }

    /**
     * Return a standardized error response.
     */
    protected function respondError(string $message, int $status = 400, mixed $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }

    /**
     * Return a 404 not found response.
     */
    protected function respondNotFound(string $message = 'Resource not found.'): JsonResponse
    {
        return $this->respondError($message, 404);
    }

    /**
     * Return a 401 unauthenticated response.
     */
    protected function respondUnauthorized(string $message = 'Unauthenticated.'): JsonResponse
    {
        return $this->respondError($message, 401);
    }

    /**
     * Return a 403 forbidden response.
     */
    protected function respondForbidden(string $message = 'This action is unauthorized.'): JsonResponse
    {
        return $this->respondError($message, 403);
    }
}
