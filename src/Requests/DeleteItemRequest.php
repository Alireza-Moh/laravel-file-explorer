<?php

namespace Alireza\LaravelFileExplorer\Requests;

use Alireza\LaravelFileExplorer\Rules\MatchDefaultDir;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DeleteItemRequest extends BaseRequest
{
    /**
     * Set validation rule
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            "items" => ["required", "array", "min:1", "max:10"],
            "items.*" => ["required"],
            "items.*.name" => ["required", "string", new MatchDefaultDir],
            "items.*.path" => ["required", "string"],
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
            'files.required' => 'Select at least one file',
            'files.array' => 'Files must be in list format',
            'files.min' => 'Select at least 1 file',
            'files.max' => 'Limit selection to a maximum of 10 files',
            'files.*.name.required' => 'File name is required',
            'files.*.name.string' => 'File name must be string',
            'files.*.path.required' => 'File path is required',
            'files.*.path.string' => 'File path must be string'
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
