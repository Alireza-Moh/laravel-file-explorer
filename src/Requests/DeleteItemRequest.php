<?php

namespace Alireza\LaravelFileExplorer\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class DeleteItemRequest extends FormRequest
{
    /**
     * Set validation rule
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            "items" => "required|array",
            "items.*" => "required",
            "items.*.name" => "required|string",
            "items.*.path" => "required|string",
            "items.*.type" => "required|string",
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        $response = response()->json([
            'message' => 'Invalid data sent',
            'errors' => $errors->messages(),
        ], 422);

        throw new HttpResponseException($response);
    }
}
