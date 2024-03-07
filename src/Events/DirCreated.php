<?php

namespace Alireza\LaravelFileExplorer\Events;

class DirCreated
{
    /**
     * @var string
     */
    private string $diskName;

    /**
     * @var string
     */
    private string $parentDirName;

    /**
     * @var string
     */
    private string $path;

    /**
     * DirCreated constructor
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
