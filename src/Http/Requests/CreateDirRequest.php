<?php

namespace AlirezaMoh\LaravelFileExplorer\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateDirRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            "path" => ["required", "string"],
            "destination" => ["required", "string"]
        ];
    }

    public function messages(): array
    {
        return [
            "path.required" => "Directory path is required",
            "path.string" => "Directory path must be a string",
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
