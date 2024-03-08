<?php

namespace AlirezaMoh\LaravelFileExplorer\Events;

class ItemUploaded
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
    private string $itemName;

    /**
     * @var string
     */
    private string $itemPath;

    /**
     * ItemUploaded constructor
     *
     * @param string $diskName
     * @param string $parentDirName
     * @param string $itemName
     * @param string $itemPath
     */
    public function __construct(string $diskName, string $parentDirName, string $itemName, string $itemPath) {
        $this->diskName = $diskName;
        $this->parentDirName = $parentDirName;
        $this->itemName = $itemName;
        $this->itemPath = $itemPath;
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
    public function getItemName(): string
    {
        return $this->itemName;
    }

    /**
     * @return string
     */
    public function getItemPath(): string
    {
        return $this->itemPath;
    }
}
