<?php

namespace Alireza\LaravelFileExplorer\Requests;

use Alireza\LaravelFileExplorer\Services\ExplorerConfig;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\File;

class CreateFileRequest extends FormRequest
{
    public function rules(): array
    {
        return $this->getRules();
    }

    /**
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        $response = response()->json([
            'message' => 'Invalid data send',
            'errors' => $errors->messages(),
        ], 422);

        throw new HttpResponseException($response);
    }

    private function getRules(): array
    {
        $config = new ExplorerConfig();
        $maxFileSize = $config->getMaxAllowedFileSize();
        $allowedFileExtensions = $config->getAllowedFileExtensions();

        $rules = [
            "path" => ["required", "string"],
            "type" => "required|string",
        ];

        if ($allowedFileExtensions !== null) {
            $rules['path'][] = File::types($allowedFileExtensions);
        }

        if ($maxFileSize !== null) {
            $rules['path'][] = File::max($maxFileSize);
        }

        if ($allowedFileExtensions !== null && $maxFileSize !== null) {
            $rules['path'][] = File::types($allowedFileExtensions)->max($maxFileSize);
        }

        return $rules;
    }
}
