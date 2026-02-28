<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Success response (snake_case keys).
     */
    protected function success($data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        $body = [
            'success' => true,
            'message' => $message,
        ];
        if ($data !== null) {
            $body['data'] = $data;
        }
        return response()->json($body, $code);
    }

    /**
     * Error response (consistent format).
     */
    protected function error(string $message, int $code = 400, $errors = null): JsonResponse
    {
        $body = [
            'success' => false,
            'message' => $message,
        ];
        if ($errors !== null) {
            $body['errors'] = $errors;
        }
        return response()->json($body, $code);
    }
}
