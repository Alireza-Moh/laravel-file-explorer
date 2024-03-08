<?php

namespace AlirezaMoh\LaravelFileExplorer\Events;

class ItemsDownloaded
{
    /**
     * @var string
     */
    protected string $diskName;

    /**
     * @var array
     */
    private array $items;

    /**
     * ItemsDownloaded constructor
     *
     * @param string $diskName
     * @param array $items
     */
    public function __construct(string $diskName, array $items)
    {
        $this->diskName = $diskName;
        $this->items = $items;
    }

    /**
     * @return string
     */
    public function getDiskName(): string
    {
        return $this->diskName;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
