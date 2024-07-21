<?php

namespace AlirezaMoh\LaravelFileExplorer\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PathRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            "path" => ["required", "string"]
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'path' => urldecode($this->query('path')),
        ]);
    }

    public function messages(): array
    {
        return [
            "path.required" => "File or Directory path is missing",
            "path.string" => "File or Directory path must be a string"
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = $this->getFailureResponse();
        throw new HttpResponseException($response);
    }
}
