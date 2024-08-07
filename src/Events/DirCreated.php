<?php

namespace AlirezaMoh\LaravelFileExplorer\Events;

class DirCreated
{
    public string $diskName;

    public string $path;

    public function __construct(string $diskName, string $path)
    {
        $this->diskName = $diskName;
        $this->path = $path;
    }
}
