<?php

namespace AlirezaMoh\LaravelFileExplorer\Rules;

use AlirezaMoh\LaravelFileExplorer\Supports\ConfigRepository;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MatchDefaultDir implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $defaultDir = ConfigRepository::getDefaultDirectoryOnLoading();

        if ($value === $defaultDir) {
            $fail("Default directory '$value' cannot be deleted or rename as it is required for initialization");
        }
    }
}
