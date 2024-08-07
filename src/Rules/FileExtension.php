<?php

namespace AlirezaMoh\LaravelFileExplorer\Rules;

use AlirezaMoh\LaravelFileExplorer\Supports\ConfigRepository;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class FileExtension implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $extension = pathinfo($value, PATHINFO_EXTENSION);

        if (!in_array($extension, ConfigRepository::getAllowedFileExtensions())) {
            $fail('The ' . $extension . 'extension is not allowed.');
        }
    }
}
