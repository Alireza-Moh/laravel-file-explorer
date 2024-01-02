<?php

namespace Alireza\LaravelFileExplorer\Requests;

use Alireza\LaravelFileExplorer\Rules\FileExtensionRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateFileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "path" => [
                "required",
                "string",
                new FileExtensionRule()
            ],
            "type" => "required|string",
        ];
    }

    /**
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        $response = response()->json([
            'message' => 'Invalid data send',
            'errors' => $errors->messages(),
        ], 422);

        throw new HttpResponseException($response);
    }
}
