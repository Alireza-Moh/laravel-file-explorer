<?php

namespace AlirezaMoh\LaravelFileExplorer\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DownloadFileRequest extends BaseRequest
{
    /**
     * Set validation rule
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            "items" => ["required", "array"],
            "items.*.name" => ["required", "string"],
            "items.*.type" => ["required", "string", "in:file,dir"],
            "items.*.path" => ["required", "string"]
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
            "items.required" => "Select a file",
            "items.array" => "Select a file",
            "items.*.name.string" => "Invalid file name format",
            "items.*.name.required" => "File name is required",
            "items.*.type.required" => "File type is required",
            "items.*.type.in" => "Invalid file type",
            "items.*.type.string" => "File type is not a string",
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
        $errors = $this->makeErrorsFriendly();
        $response = $this->getFailureResponse($errors);

        throw new HttpResponseException($response);
    }

    /**
     * Map errors to corresponding files based on the file index in the input array.
     *
     * @return array the modified errors
     */
    private function makeErrorsFriendly(): array
    {
        $files = $this->input('items');
        $fileErrorsMap = [];

        $errors = $this->validator->errors()->messages();
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
