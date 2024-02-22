<?php

namespace Alireza\LaravelFileExplorer\Requests;

use Alireza\LaravelFileExplorer\Rules\FileExtension;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateFileRequest extends BaseRequest
{
    /**
     * Set validation rule
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            "path" => ["required", "string", new FileExtension],
            "destination" => ["required", "string"]
        ];
    }

    /**
     * Set validation error message
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            "path.required" => "File path is required",
            "path.string" => "File path must be a string",
            "destination.required" => "Destination directory is required",
            "destination.string" => "Destination directory must be a string"
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
        $response = $this->getFailureResponse();
        throw new HttpResponseException($response);
    }
}
