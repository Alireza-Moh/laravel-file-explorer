<?php

namespace AlirezaMoh\LaravelFileExplorer\Services\Contracts;

use Illuminate\Http\JsonResponse;

interface ItemUtil
{
    public function create(string $diskName, array $validatedData): JsonResponse;

    public function delete(string $diskName, array $validatedData): JsonResponse;
}
