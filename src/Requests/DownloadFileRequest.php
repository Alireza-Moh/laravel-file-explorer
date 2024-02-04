<?php

namespace Alireza\LaravelFileExplorer\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class DownloadFileRequest extends FormRequest
{
    /**
     * Set validation rule
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            "items" => "required|array",
            "items.*.name" => "required|string",
            "items.*.type" => "required|string|in:file",
            "items.*.path" => "required|string"
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
            "items.required" => "Please select a file",
            "items.array" => "Please select a file",
            "items.*.name.string" => "Invalid file name format",
            "items.*.name.required" => "File name is required",
            "items.*.type.required" => "File type is required",
            "items.*.type.in" => "Invalid file type",
            "items.*.type.string" => "Invalid file type format",
            "items.*.path.required" => "File path is required",
            "items.*.path.string" => "Invalid file path format",
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
        $errors = $validator->errors();

        $response = response()->json([
            'message' => 'Invalid data sent',
            "errors" => $this->makeErrorsFriendly($errors->messages())
        ], 422);

        throw new HttpResponseException($response);
    }

    /**
     * Map errors to corresponding files based on the file index in the input array.
     *
     * @param array $errors actual errors
     * @return array the modified errors
     */
    private function makeErrorsFriendly(array $errors): array
    {
        $files = $this->input('items');
        $fileErrorsMap = [];

        foreach ($errors as $errorKey => $errorMessages) {
            if (preg_match('/items\.(\d+)\.(\w+)/', $errorKey, $matches) && count($matches) > 2) {
                $fileIndex = $matches[1];

                if (isset($files[$fileIndex])) {
                    $fileName = $files[$fileIndex]['name'];
                    $fileErrorsMap[$fileName] = array_merge($fileErrorsMap[$fileName] ?? [], $errorMessages);
                }
            }
        }

        return $fileErrorsMap;
    }
}
