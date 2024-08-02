<?php

namespace AlirezaMoh\LaravelFileExplorer\Supports;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiResponse
{
    const STATUS_SUCCESS = 'success';

    const STATUS_FAILED = 'failed';

    public static function success(string $message, array $additionalData = []): JsonResponse
    {
        return response()->json([
            'status' => self::STATUS_SUCCESS,
            'message' => $message,
            'result' => $additionalData
        ], Response::HTTP_OK);
    }

    public static function error(string $message, array $additionalData = []): JsonResponse
    {
        return response()->json([
            'status' => self::STATUS_FAILED,
            'message' => $message,
            'result' => $additionalData
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public static function forbidden(string $message, array $additionalData = []): JsonResponse
    {
        return response()->json([
            'status' => self::STATUS_FAILED,
            'message' => $message,
            'result' => $additionalData
        ], Response::HTTP_FORBIDDEN);
    }
}
