<?php

namespace Alireza\LaravelFileExplorer\Requests;

use Alireza\LaravelFileExplorer\Services\ConfigRepository;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\File;

class UploadFilesRequest extends FormRequest
{
    /**
     * Set validation rule
     *
     * @return array
     */
    public function rules(): array
    {
        return $this->getRules();
    }

    /**
     * Set validation error message
     *
     * @return array
     */
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
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
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
            'message' => 'Invalid data sent',
            'errors' => $errors
        ], 422);

        throw new HttpResponseException($response);
    }

    /**
     * Get validation rules based on the settings in the repo config file
     *
     * @return array
     */
    private function getRules(): array
    {
        $maxFileSize = ConfigRepository::getMaxAllowedFileSize();
        $allowedFileExtensions = ConfigRepository::getAllowedFileExtensions();

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
