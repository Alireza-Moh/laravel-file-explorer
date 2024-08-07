<?php

namespace AlirezaMoh\LaravelFileExplorer\Http\Requests;

use AlirezaMoh\LaravelFileExplorer\Rules\FileExtension;
use AlirezaMoh\LaravelFileExplorer\Rules\MatchDefaultDir;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RenameItemRequest extends BaseRequest
{
    public function rules(): array
    {
        return array_merge(
            [
                'type' => ['required', 'string'],
                'oldName' => ['required', 'string', new MatchDefaultDir],
                'oldPath' => ['required', 'string'],
                'parent' => ['string', 'nullable'],
            ],
            $this->getRules()
        );
    }

    public function messages(): array
    {
        return [
            'oldName.required' => 'Old file/directory name is required',
            'oldName.string' => 'Old file/directory name must be string',
            'newName.required' => 'New file/directory name is required',
            'newName.string' => 'New file/directory name must be string',
            'oldPath.required' => 'Old file/directory path is required',
            'oldPath.string' => 'Old file/directory path must be string',
            'newPath.required' => 'New file/directory path is required',
            'newPath.string' => 'New file/directory path must be string',
            'type.required' => 'File type is required',
            'type.string' => 'File type must be string',
            'parent.string' => 'Parent directory name must be string'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = $this->getFailureResponse();
        throw new HttpResponseException($response);
    }

    private function getRules(): array
    {
        $rules = [
            'newName' => ['required', 'string'],
            'newPath' => ['required', 'string']
        ];

        if ($this->type === 'file') {
            foreach ($rules as $key => $value) {
                $rules[$key][] = new FileExtension();
            }
        }

        return $rules;
    }
}
