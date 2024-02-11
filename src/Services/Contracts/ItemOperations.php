<?php

namespace Alireza\LaravelFileExplorer\Services\Contracts;

interface ItemOperations
{
    /**
     * Create a new item (file or directory) on the specified disk.
     *
     * @param string $diskName The name of the disk where the item will be created.
     * @param array $validatedData Validated data needed for the creation operation.
     * @return mixed The result of the creation operation.
     */
    public function create(string $diskName, array $validatedData): array;

    /**
     * Delete an item from the specified disk.
     *
     * @param string $diskName The name of the disk where the item exists.
     * @param array $validatedData Validated data needed for the deletion operation.
     * @return mixed The result of the deletion operation.
     */
    public function delete(string $diskName, array $validatedData): array;
}
