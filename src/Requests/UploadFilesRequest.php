<?php

namespace Alireza\LaravelFileExplorer\Requests;

use Alireza\LaravelFileExplorer\Services\ExplorerConfig;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\File;

class UploadFilesRequest extends FormRequest
{
    public function rules(): array
    {
        return $this->getRules();
    }

    public function messages(): array
    {
        return [
            "ifFileExist.required" => "Choose an action",
            "ifFileExist.numeric" => "Choose an action",
            "ifFileExist.in" => "Action not valid",
            "files.*.required" => "Please select a file",
            "files.*.file" => "It is  not a file",
            "files.*.max" => "Max file size reached",
            "files.*.mimes" => "File extension not allowed",
            "destination.required" => "File destination path is required",
            "destination.string" => "File destination path is not a string",
        ];
    }

    /**
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = [];

        foreach ($validator->errors()->messages() as $message) {
            foreach ($this->validationData()["files"] as $file) {
                $errors[$file->getClientOriginalName()] = $message;
            }
        }

        $response = response()->json([
            'message' => 'Invalid data send',
            'errors' => $errors
        ], 422);

        throw new HttpResponseException($response);
    }

    private function getRules(): array
    {
        $config = new ExplorerConfig();
        $maxFileSize = $config->getMaxAllowedFileSize();
        $allowedFileExtensions = $config->getAllowedFileExtensions();

        $rules = [
            "ifFileExist" => ["required", "numeric", "in:0,1"],
            "files.*" => ["required", "file"],
            "destination" => ["required", "string"],
        ];

        if ($allowedFileExtensions !== null) {
            $rules['files.*'][] = File::types($allowedFileExtensions);
        }

        if ($maxFileSize !== null) {
            $rules['files.*'][] = File::max($maxFileSize);
        }

        return $rules;
    }
}