<?php

namespace AlirezaMoh\LaravelFileExplorer\Events;

class FileCreated
{
    /**
     * @var string
     */
    public string $diskName;

    /**
     * @var string
     */
    public string $parentDirName;

    /**
     * @var string
     */
    public string $path;

    /**
     * FileCreated constructor
     *
     * @param string $diskName
     * @param string $parentDirName
     * @param string $path
     */
    public function __construct(string $diskName, string $parentDirName, string $path)
    {
        $this->diskName = $diskName;
        $this->parentDirName = $parentDirName;
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getDiskName(): string
    {
        return $this->diskName;
    }

    /**
     * @return string
     */
    public function getParentDirName(): string
    {
        return $this->parentDirName;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
}
