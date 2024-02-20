<?php

namespace Alireza\LaravelFileExplorer\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateItemContentRequest extends BaseRequest
{
    /**
     * Set validation rule
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            "path" => ["required", "string"],
            "item" => ["required", "file"]
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
            "destination.required" => "Directory name is required",
            "destination.string" => "Directory name must be string",
            "path.required" => "Item path is required",
            "path.string" => "Item path must be string",
            "item.required" => "File is required",
            "item.file" => "Incorrect file type"
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
