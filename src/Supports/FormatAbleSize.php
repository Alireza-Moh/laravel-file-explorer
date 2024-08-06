<?php

namespace AlirezaMoh\LaravelFileExplorer\Supports;

trait FormatAbleSize
{
    protected function formatSize(float $size): string
    {
        $formattedSize = '-';
        if ($size > 0) {
            $units = array('B', 'KB', 'MB', 'GB', 'TB');
            $i = floor(log($size, 1024));
            $formattedSize = number_format($size / pow(1024, $i), 2) . ' ' . $units[$i];
        }

        return $formattedSize;
    }

    private function getDirectorySize(string $directoryPath): float
    {
        $size = 0;
        foreach ($this->storage->allFiles($directoryPath) as $file) {
            $size += $this->storage->size($file);
        }
        return $size;
    }

    private function getFileSize(string $filePath): float
    {
        return $this->storage->size($filePath);
    }
}
