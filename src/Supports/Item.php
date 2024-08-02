<?php

namespace AlirezaMoh\LaravelFileExplorer\Supports;

use Illuminate\Support\Collection;

class Item
{
    public string $diskName;

    public ?string $parent;

    public string $name;

    public string $path;

    public string $type;

    public float $size;
    public string $formattedSize;

    public string $content;

    public string $url;

    public ?string $extension;

    public bool $isChecked;

    public string $lastModified;

    public Collection $subDir;

    public function __construct(string $diskName, ?string $parent, string $name, string $path, string $type, float $size, string $formattedSize, string $url, string|null $extension, bool $isChecked, string $lastModified, Collection $subDir)
    {
        $this->diskName = $diskName;
        $this->parent = $parent;
        $this->name = $name;
        $this->path = $path;
        $this->type = $type;
        $this->size = $size;
        $this->formattedSize = $formattedSize;
        $this->url = $url;
        $this->extension = $extension;
        $this->isChecked = $isChecked;
        $this->lastModified = $lastModified;
        $this->subDir = $subDir;
    }
}
