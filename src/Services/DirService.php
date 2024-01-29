<?php
namespace Alireza\LaravelFileExplorer\Services;

use Alireza\LaravelFileExplorer\Services\Contracts\ItemOperations;
use Alireza\LaravelFileExplorer\Utilities\DirManager;
use Illuminate\Support\Facades\Storage;

class DirService extends BaseItemManager implements ItemOperations
{
    use DirManager;

    /**
     * Retrieve items within a directory.
     *
     * @param string $diskName
     * @param string $dirName
     *
     * @return array items
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
     *
     * @return array containing directories on the disk.
     */
    public function getDiskDirsForTree(string $diskName, string $dirName = ''): array
    {
        $dirs = Storage::disk($diskName)->directories($dirName);
        $allDirs = [];

        foreach ($dirs as $dir) {
            $subDirs = $this->getDiskDirsForTree($diskName, $dir);
            $allDirs[] = [
                "diskName" => $diskName,
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
     *
     * @return array|null The found directory or null if not found.
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
     * @param array $validatedData dirs to delete
     *
     * @return array
     */
    public function delete(string $diskName, array $validatedData): array
    {
        if ($this->existDefaultDirOnLoadingInArray($validatedData["items"])) {
            return [
                "status" => "failed",
                "message" => "You cannot delete the default directory because it's needed for initiation"
            ];
        }
        $storage = Storage::disk($diskName);
        foreach ($validatedData["items"] as $dir) {
            $storage->deleteDirectory($dir["path"]);
        }

        return $this->getResponse(true, success: "Directory deleted successfully");
    }

    /**
     * Rename a directory.
     *
     * @param string $diskName
     * @param string $oldName The old name of the directory.
     * @param array $validatedData
     *
     * @return array The result of the rename operation.
     */
    public function rename(string $diskName, string $oldName, array $validatedData): array
    {
        $defaultDirOnLoading = config('laravel-file-explorer.default_directory_on_loading');
        if ($this->isDefaultDirectory($defaultDirOnLoading, $oldName)) {
            return $this->getResponse(
                false,
                failure: "You cannot rename the default directory because it's needed for initiation."
            );
        }
        $result = Storage::disk($diskName)->move($validatedData["oldPath"], $validatedData["newPath"]);

        return $this->getResponse(
            $result,
            'Directory renamed successfully',
            'Failed to rename directory'
        );
    }

    /**
     * Create a directory
     *
     * @param string $diskName
     * @param array $validatedData Validated data for directory creation.
     *
     * @return array Result of the directory creation.
     */
    public function create(string $diskName, array $validatedData): array
    {
        $result = Storage::disk($diskName)->makeDirectory($validatedData["path"]);
        $message = $result ? "Directory created successfully" : "Failed to create directory";

        return $this->getCreationResponse($diskName, $result, $message, $validatedData["destination"]);
    }

    /**
     * Retrieve metadata for a specific item.
     *
     * @param string $diskName
     * @param string $path
     * @param string $type
     * @return array Metadata information for the item.
     */
    private function getMetaData(string $diskName, string $path, string $type): array
    {
        $url = Storage::disk($diskName)->url($path);
        $commonMetaData = [
            'diskName' => $diskName,
            'name' => basename($path),
            'path' => $path,
            'type' => $type,
            'size' => "-",
            'lastModified' => "-",
            'extension' => null,
            'url' => $url,
        ];

        if ($type === 'file') {
            $commonMetaData['size'] = $this->getFileSizeInKB($diskName, $path);
            $commonMetaData['lastModified'] = $this->getLastModified($diskName, $path);
            $commonMetaData['extension'] = pathinfo($path, PATHINFO_EXTENSION);
        }

        return $commonMetaData;
    }
}
