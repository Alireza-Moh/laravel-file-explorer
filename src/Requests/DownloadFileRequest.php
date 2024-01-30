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
            "files" => "required|array",
            "files.*" => "required",
            "files.*.name" => "required|string",
            "files.*.type" => "required|string|in:file",
            "files.*.path" => "required|string"
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
            "files.required" => "Please select a file",
            "files.*.required" => "Please select a file",
            "files.*.name.string" => "File name is not a string",
            "files.*.name.required" => "File name is missing",
            "files.*.type.required" => "File type is missing",
            "files.*.type.in" => "It is not a file",
            "files.*.type.string" => "File type is not a string",
            "files.*.path.required" => "File path is missing",
            "files.*.path.string" => "File path is not a string",
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
     *
     * @return array the modified errors
     */
    private function makeErrorsFriendly(array $errors): array
    {
        $files = $this->input('files');
        $fileErrorsMap = [];

        foreach ($errors as $errorKey => $errorMessages) {
            if (preg_match('/files\.(\d+)\.(\w+)/', $errorKey, $matches) && count($matches) > 2) {
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
