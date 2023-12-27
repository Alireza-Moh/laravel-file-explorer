<?php

namespace Alireza\LaravelFileExplorer\Services\Contracts;

interface BaseStorage
{
    public function create(string $diskName, array $validatedData);
    public function rename(string $diskName, array $validatedData);
    public function update(string $diskName, array $validatedData);
    public function delete(string $diskName, array $validatedData);
    public function get(string $diskName, array $validatedData);
}
