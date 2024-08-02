<?php

namespace AlirezaMoh\LaravelFileExplorer\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateItemContentRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'path' => ['required', 'string'],
            'item' => ['required', 'file']
        ];
    }

    public function messages(): array
    {
        return [
            'path.required' => 'File path is required',
            'path.string' => 'File path must be string',
            'item.required' => 'File is required',
            'item.file' => 'Incorrect file type'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = $this->getFailureResponse();
        throw new HttpResponseException($response);
    }
}
