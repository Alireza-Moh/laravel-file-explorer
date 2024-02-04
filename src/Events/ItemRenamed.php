<?php

namespace Alireza\LaravelFileExplorer\Events;

class ItemRenamed
{
    /**
     * @var string
     */
    private string $diskName;

    /**
     * @var string
     */
    private string $oldName;

    /**
     * @var string
     */
    private string $newName;

    /**
     * @var string
     */
    private string $oldPath;

    /**
     * @var string
     */
    private string $newPath;

    /**
     * ItemRenamed constructor
     *
     * @param string $diskName
     * @param string $oldName
     * @param array $item
     */
    public function __construct(string $diskName, string $oldName, array $item) {
        $this->diskName = $diskName;
        $this->oldName = $oldName;
        $this->newName = $item["newName"];
        $this->oldPath = $item["oldPath"];
        $this->newPath = $item["newPath"];
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
    public function getOldName(): string
    {
        return $this->oldName;
    }

    /**
     * @return string
     */
    public function getNewName(): string
    {
        return $this->newName;
    }

    /**
     * @return string
     */
    public function getOldPath(): string
    {
        return $this->oldPath;
    }

    /**
     * @return string
     */
    public function getNewPath(): string
    {
        return $this->newPath;
    }
}
