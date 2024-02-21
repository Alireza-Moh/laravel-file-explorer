<?php

namespace Alireza\LaravelFileExplorer\Requests;

use Alireza\LaravelFileExplorer\Rules\MatchDefaultDir;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RenameItemRequest extends BaseRequest
{
    /**
     * Set validation rule
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            "oldName" => ["required", "string", new MatchDefaultDir],
            "newName" => ["required", "string"],
            "oldPath" => ["required", "string"],
            "newPath" => ["required", "string"]
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
            "oldName.required" => "Old file/directory name is required",
            "oldName.string" => "Old file/directory name must be string",
            "newName.required" => "New file/directory name is required",
            "newName.string" => "New file/directory name must be string",
            "oldPath.required" => "Old file/directory path is required",
            "oldPath.string" => "Old file/directory path must be string",
            "newPath.required" => "New file/directory path is required",
            "newPath.string" => "New file/directory path must be string"
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
