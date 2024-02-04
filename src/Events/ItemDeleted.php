<?php

namespace Alireza\LaravelFileExplorer\Events;

class ItemDeleted
{
    /**
     * @var string
     */
    private string $diskName;

    /**
     * @var string
     */
    private string $itemName;

    /**
     * @var string
     */
    private string $path;

    /**
     * ItemDeleted constructor
     *
     * @param string $diskName
     * @param string $itemName
     * @param string $path
     */
    public function __construct(string $diskName, string $itemName, string $path)
    {
        $this->diskName = $diskName;
        $this->itemName = $itemName;
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getDiskName(): string
    {
        return $this->diskName;
    }

    public function getItemName(): string
    {
        return $this->itemName;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
}
