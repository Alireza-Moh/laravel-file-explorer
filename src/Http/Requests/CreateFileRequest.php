<?php

namespace AlirezaMoh\LaravelFileExplorer\Http\Requests;

use AlirezaMoh\LaravelFileExplorer\Rules\FileExtension;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateFileRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            "path" => ["required", "string", new FileExtension],
            "destination" => ["required", "string"]
        ];
    }

    public function messages(): array
    {
        return [
            "path.required" => "File path is required",
            "path.string" => "File path must be a string",
            "destination.required" => "Destination directory is required",
            "destination.string" => "Destination directory must be a string"
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = $this->getFailureResponse();
        throw new HttpResponseException($response);
    }
}
