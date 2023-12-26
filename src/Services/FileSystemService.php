<?php

namespace Alireza\LaravelFileExplorer\Services;

use Illuminate\Support\Facades\Storage;

class FileSystemService
{
    /**
     * Rename a directory.
     *
     * @param string $diskName The name of the disk.
     * @param string $oldName The old directory name.
     * @param string $oldPath  The old directory path.
     * @param string $newPath  The new directory path.
     *
     * @return array The result of the rename operation.
     */
    public function renameDir(string $diskName, string $oldName, string $oldPath, string $newPath): array
    {
        $defaultDirOnLoading = config('laravel-file-explorer.default_directory_from_default_disk_on_loading');

        if ($this->isDefaultDirectory($defaultDirOnLoading, $oldName)) {
            return $this->getResult(false, "", "You cannot rename the default directory because it's needed for initiation.");
        }

        $result = Storage::disk($diskName)->move($oldPath, $newPath);

        return $this->getResult($result, 'Directory renamed successfully', 'Failed to rename directory');
    }

    /**
     * Delete a directory.
     *
     * @param string $diskName The name of the disk
     * @param string $dirName The directory name
     * @param string $path     The path of the directory to delete.
     *
     * @return array The result of the delete operation.
     */
    public function deleteDir(string $diskName, string $dirName, string $path): array
    {
        $defaultDirOnLoading = config('laravel-file-explorer.default_directory_from_default_disk_on_loading');

        if ($this->isDefaultDirectory($defaultDirOnLoading, $dirName)) {
            return $this->getResult(false, "", "You cannot delete the default directory because it's needed for initiation.");
        }

        $result = Storage::disk($diskName)->deleteDirectory($path);

        return $this->getResult($result, 'Directory deleted successfully', 'Failed to delete directory');
    }

    /**
     * Get the response based on the operation result
     *
     * @param bool   $result  The result of the operation.
     * @param string $success The success message.
     * @param string $failure The failure message.
     *
     * @return array The response
     */
    private function getResult(bool $result, string $success, string $failure): array
    {
        return [
            "result" => [
                'status'  => $result ? "success" : "failed",
                'message' => $result ? $success : $failure,
            ]
        ];
    }

    /**
     * Check if the directory is the default directory on loading.
     *
     * @param string|null $defaultDirOnLoading The default directory on loading.
     * @param string      $dirName             The directory name to check.
     *
     * @return bool Whether the directory is the default directory.
     */
    private function isDefaultDirectory(?string $defaultDirOnLoading, string $dirName): bool
    {
        return $defaultDirOnLoading && $defaultDirOnLoading == $dirName;
    }
}
