<?php

namespace Alireza\LaravelFileExplorer\Services\Contracts;

interface ItemUtil
{
    /**
     * Create a new item (file or directory) on the specified disk.
     *
     * @param string $diskName
     * @param array $validatedData
     * @return mixed
     */
    public function create(string $diskName, array $validatedData): array;

    /**
     * Delete an item (file or directory) from the specified disk.
     *
     * @param string $diskName
     * @param array $validatedData
     * @return mixed
     */
    public function delete(string $diskName, array $validatedData): array;
}
