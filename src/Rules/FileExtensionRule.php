<?php

namespace Alireza\LaravelFileExplorer\Rules;

use Alireza\LaravelFileExplorer\Services\ConfigRepository;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class FileExtensionRule implements ValidationRule
{
    /**
     * Validate the file extension.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $extension = pathinfo($value, PATHINFO_EXTENSION);

        if (!in_array($extension, ConfigRepository::getAllowedFileExtensions())) {
            $fail("The $extension extension is not allowed.");
        }
    }
}
