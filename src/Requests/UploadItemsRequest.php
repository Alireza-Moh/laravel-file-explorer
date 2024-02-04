<?php

namespace Alireza\LaravelFileExplorer\Requests;

use Alireza\LaravelFileExplorer\Services\ConfigRepository;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;

class UploadItemsRequest extends FormRequest
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
            "ifItemExist.required" => "Choose an action",
            "ifItemExist.numeric" => "Choose a valid action",
            "ifItemExist.in" => "Invalid action selected",
            "items.required" => "Please select a file",
            "items.array" => "Please select a file",
            "items.*.file" => "Invalid file format",
            "items.*.max" => "item size exceeds the limit",
            "items.*.mimes" => "File extension not allowed",
            "destination.required" => "File destination path is required",
            "destination.string" => "Invalid file destination path format",
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
        $response = response()->json([
            'message' => 'Invalid data sent',
            'errors' => $this->makeErrorsFriendly($validator->errors()->messages()),
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
            "ifItemExist" => ["required", "numeric", "in:0,1"],
            "items" => ["required", "array"],
            "items.*" => ["file"],
            "destination" => ["required", "string"],
        ];

        if ($allowedFileExtensions !== null) {
            $rules['items.*'][] = File::types($allowedFileExtensions);
        }

        if ($maxFileSize !== null) {
            $rules['items.*'][] = File::max($maxFileSize);
        }

        return $rules;
    }

    /**
     * Map errors to corresponding files based on the file index in the input array.
     *
     * @param array $errors actual errors
     * @return array the modified errors
     */
    private function makeErrorsFriendly(array $errors): array
    {
        $files = $this->validationData()["items"];
        $modifiedErrors = [];
        foreach ($errors as $key => $error) {
            if (Str::startsWith($key, "items.")){
                $index = (int)explode('.', $key)[1];

                if (array_key_exists($index, $files)) {
                    $modifiedErrors[$files[$index]->getClientOriginalName()] = $error;
                }
            }
        }

        return empty($modifiedErrors) ? $errors : $modifiedErrors;
    }
}
