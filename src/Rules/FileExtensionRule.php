<?php

namespace Alireza\LaravelFileExplorer\Rules;

use Alireza\LaravelFileExplorer\Services\ExplorerConfig;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class FileExtensionRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $explorerConfig = new ExplorerConfig();

        $extension = pathinfo($value, PATHINFO_EXTENSION);

        if (!in_array($extension, $explorerConfig->getAllowedFileExtensions())) {
            $fail("The $extension extension is not allowed.");
        }
    }
}
