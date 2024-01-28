<?php

namespace Alireza\LaravelFileExplorer\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class DownloadFileRequest extends FormRequest
{
    /**
     * Set validation rule
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            "files" => "required|array",
            "files.*" => "required",
            "files.*.type" => "required|string",
            "files.*.path" => "required|string"
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
