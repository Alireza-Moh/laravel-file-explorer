<?php

namespace AlirezaMoh\LaravelFileExplorer\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BaseRequest extends FormRequest
{
    protected function getFailureResponse(array $customErrors = [], int $status = Response::HTTP_UNPROCESSABLE_ENTITY): JsonResponse
    {
        if (empty($customErrors)) {
            $customErrors = $this->validator->errors()->messages();
        }

        return response()->json([
            'message' => 'Invalid data sent',
            'errors' => $customErrors,
        ], $status);
    }
}
