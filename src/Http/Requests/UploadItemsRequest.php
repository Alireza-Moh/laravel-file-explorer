<?php

namespace AlirezaMoh\LaravelFileExplorer\Http\Requests;

use AlirezaMoh\LaravelFileExplorer\Supports\ConfigRepository;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;

class UploadItemsRequest extends BaseRequest
{
    public function rules(): array
    {
        return $this->getRules();
    }

    public function messages(): array
    {
        return [
            'ifItemExist.required' => 'Choose an action overwrite/skip',
            'ifItemExist.numeric' => 'Action is not valid',
            'ifItemExist.in' => 'Invalid action selected',
            'items.required' => 'Select a file',
            'items.array' => 'Select a file',
            'items.*.file' => 'Invalid file format',
            'items.*.max' => 'File size exceeds the limit',
            'items.*.mimes' => 'File extension not allowed',
            'destination.required' => 'File destination path is required',
            'destination.string' => 'File destination path must be a string',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $this->makeErrorsFriendly();
        $response = $this->getFailureResponse($errors);

        throw new HttpResponseException($response);
    }

    private function getRules(): array
    {
        $maxFileSize = ConfigRepository::getMaxAllowedFileSize();
        $allowedFileExtensions = ConfigRepository::getAllowedFileExtensions();

        $rules = [
            'ifItemExist' => ['required', 'numeric', 'in:0,1'],
            'items' => ['required', 'array'],
            'items.*' => ['file'],
            'destination' => ['required', 'string'],
        ];

        if ($allowedFileExtensions !== null) {
            $rules['items.*'][] = 'mimes:' . implode(',', $allowedFileExtensions);
        }

        if ($maxFileSize !== null) {
            $rules['items.*'][] = 'max:$maxFileSize';
        }

        return $rules;
    }

    private function makeErrorsFriendly(): array
    {
        $files = $this->validationData()['items'];
        $modifiedErrors = [];
        $errors = $this->validator->errors()->messages();
        foreach ($errors as $key => $error) {
            if (Str::startsWith($key, 'items.')){
                $index = (int)explode('.', $key)[1];

                if (array_key_exists($index, $files)) {
                    $modifiedErrors[$files[$index]->getClientOriginalName()] = $error;
                }
            }
        }

        return empty($modifiedErrors) ? $errors : $modifiedErrors;
    }
}
