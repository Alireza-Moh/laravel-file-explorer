<?php

namespace Alireza\LaravelFileExplorer\Services;

use Illuminate\Support\Facades\Storage;

class FileSystemService
{
    /**
     * Rename a directory.
     *
     * @param string $diskName The name of the disk.
     * @param string $oldName The old name of the directory.
     * @param array $validatedData contains new and old path .
     *
     * @return array The result of the rename operation.
     */
    public function renameDir(string $diskName, string $oldName, array $validatedData): array
    {
        $defaultDirOnLoading = config('laravel-file-explorer.default_directory_from_default_disk_on_loading');

        if ($this->isDefaultDirectory($defaultDirOnLoading, $oldName)) {
            return $this->getResult(false, "", "You cannot rename the default directory because it's needed for initiation.");
        }

        $result = Storage::disk($diskName)->move($validatedData["oldPath"], $validatedData["newPath"]);

        return $this->getResult($result, 'Directory renamed successfully', 'Failed to rename directory');
    }

    /**
     * Create a file or directory.
     *
     * @param string $diskName The disk name.
     * @param string $dirName The directory name.
     * @param array $validatedData Validated data containing "path" and "type" (file or dir).
     *
     * @return array Result of the operation with status, message, and updated items.
     */
    public function create(string $diskName, string $dirName, array $validatedData): array
    {
        $result = null;
        $message = null;

        switch ($validatedData["type"]) {
            case "file":
                $result = Storage::disk($diskName)->put($validatedData["path"], "");
                $message = $result ? "File created successfully" : "Failed to create file";
                break;
            case "dir":
                $result = Storage::disk($diskName)->makeDirectory($validatedData["path"]);
                $message = $result ? "Directory created successfully" : "Failed to create directory";
                break;
            default:
                return [
                    "result" => [
                        'status' => "failed",
                        'message' => "Invalid type",
                        'items' => []
                    ]
                ];
        }

        list($dirs, $items) = $this->getRefreshedDiskData($diskName, $validatedData["dirPath"]);
        return [
            "result" => [
                'status' => $result ? "success" : "failed",
                'message' => $message,
                'items' => $items,
                "dirs" => $dirs,
            ]
        ];
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

    /**
     * Get items within a directory.
     *
     * @param string $diskName The disk name.
     * @param string $dirName The directory name.
     *
     * @return array Items within the specified directory.
     */
    private function getRefreshedDiskData(string $diskName, string $dirName): array
    {
        $dirService = new DirService($diskName);

        return array($dirService->getDiskDirs(), $dirService->getDirItems($dirName));
    }
}
