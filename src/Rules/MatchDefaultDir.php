<?php

namespace Alireza\LaravelFileExplorer\Rules;

use Alireza\LaravelFileExplorer\Services\ConfigRepository;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MatchDefaultDir implements ValidationRule
{
    /**
     * Validate if user is not renaming the default directory.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $defaultDir = ConfigRepository::getDefaultDirectoryOnLoading();

        if ($value === $defaultDir) {
            $fail("Default directory '$value' cannot be deleted or rename as it is required for initialization");
        }
    }
}
