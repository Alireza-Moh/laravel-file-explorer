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

    public function messages(): array
    {
        return [
            'items.required' => 'Select at least one item',
            'items.array' => 'Items must be in list format',
            'items.min' => 'Select at least 1 item',
            'items.max' => 'Select at most 10 items',
            'items.*.name.required' => 'Item name is required for all selected items',
            'items.*.name.string' => 'Item names must be strings',
            'items.*.path.required' => 'Item path is required for all selected items',
            'items.*.path.string' => 'Item paths must be string'
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
