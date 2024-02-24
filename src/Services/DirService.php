<?php
namespace Alireza\LaravelFileExplorer\Services;

use Alireza\LaravelFileExplorer\Events\DirCreated;
use Alireza\LaravelFileExplorer\Services\Contracts\ItemUtil;
use Alireza\LaravelFileExplorer\Supports\Traits\DirManager;
use Illuminate\Support\Facades\Storage;

class DirService extends BaseItemManager implements ItemUtil
{
    use DirManager;

    /**
     * Retrieve items within a directory.
     *
     * @param string $diskName
     * @param string $dirName
     * @return array
     */
    public function getDirItems(string $diskName, string $dirName): array
    {
        return array_merge(
            $this->getDirItemsByType($diskName, 'file', dirName: $dirName),
            $this->getDirItemsByType($diskName, 'dir', dirName: $dirName)
        );
    }

    /**
     * Get directories within the disk.
     *
     * @param string $diskName
     * @param string $dirName
     * @return array
     */
    public function getDiskDirsForTree(string $diskName, string $dirName = ''): array
    {
        $dirs = Storage::disk($diskName)->directories($dirName);
        $allDirs = [];

        foreach ($dirs as $dir) {
            $subDirs = $this->getDiskDirsForTree($diskName, $dir);
            $allDirs[] = [
                "diskName" => $diskName,
                "dirName" => $dirName,
                "name" => basename($dir),
                "path" => $dir,
                "type" => "dir",
                "subDir" => $subDirs,
            ];
        }
        return $allDirs;
    }

    /**
     * Get files within the disk.
     *
     * @param string $diskName
     * @return array
     */
    public function getDiskItems(string $diskName): array
    {
        return array_merge(
            $this->getDirItemsByType($diskName, 'file', false),
            $this->getDirItemsByType($diskName, 'dir', false)
        );
    }

    /**
     * Find a directory by name.
     *
     * @param string $diskName
     * @param string $dirName
     * @param array $dirs
     * @return array|null
     */
    public function findDirectoryByName(string $diskName, string $dirName, array $dirs = []): ?array
    {
        if (empty($dirs)) {
            $dirs = $this->getDiskDirsForTree($diskName);
        }

        foreach ($dirs as $directory) {
            if ($directory['name'] === $dirName) {
                return $directory;
            }

            if (!empty($directory['subDir'])) {
                $foundInSubDir = $this->findDirectoryByName($diskName, $dirName, $directory['subDir']);
                if ($foundInSubDir !== null) {
                    return $foundInSubDir;
                }
            }
        }
        return null;
    }

    /**
     * Delete a directory.
     *
     * @param string $diskName
     * @param array $validatedData
     * @return array
     */
    public function delete(string $diskName, array $validatedData): array
    {
        $storage = Storage::disk($diskName);
        foreach ($validatedData["items"] as $dir) {
            $result = $storage->deleteDirectory($dir["path"]);
            if ($result) {
                $this->fireDeleteEvent($diskName, $dir);
            }
        }

        return $this->getResponse(true, success: "Directory deleted successfully");
    }

    /**
     * Create a directory
     *
     * @param string $diskName
     * @param array $validatedData
     * @return array
     */
    public function create(string $diskName, array $validatedData): array
    {
        $result = Storage::disk($diskName)->makeDirectory($validatedData["path"]);
        $message = $result ? "Directory created successfully" : "Failed to create directory";

        if ($result) {
            event(new DirCreated($diskName, $validatedData["destination"], $validatedData["path"]));
        }

        return $this->getCreationResponse($diskName, $result, $message, $validatedData["destination"]);
    }
}
