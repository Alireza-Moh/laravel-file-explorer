<?php

namespace Alireza\LaravelFileExplorer\Services;

use Illuminate\Support\Facades\Storage;

class FileSystemService
{

    /**
     * Rename a directory.
     *
     * @param string $diskName
     * @param string $oldName The old name of the directory.
     * @param array $validatedData
     *
     * @return array The result of the rename operation.
     */
    public function renameDir(string $diskName, string $oldName, array $validatedData): array
    {
        $defaultDirOnLoading = config('laravel-file-explorer.default_directory_on_loading');

        if ($this->isDefaultDirectory($defaultDirOnLoading, $oldName)) {
            return $this->getResult(false, "", "You cannot rename the default directory because it's needed for initiation.");
        }

        $result = Storage::disk($diskName)->move($validatedData["oldPath"], $validatedData["newPath"]);

        return $this->getResult($result, 'Directory renamed successfully', 'Failed to rename directory');
    }

    /**
     * Create a file or directory.
     *
     * @param string $diskName
     * @param string $dirName
     * @param array $validatedData
     *
     * @return array Result of the operation
     */
    public function create(string $diskName, string $dirName, array $validatedData): array
    {
        return match ($validatedData["type"]) {
            "file" => $this->createFile($diskName, $validatedData),
            "dir" => $this->createDir($diskName, $validatedData),
            default => $this->invalidTypeResponse(),
        };
    }

    /**
     * Create a file and return the result.
     *
     * @param string $diskName The disk name.
     * @param array $validatedData Validated data for file creation.
     *
     * @return array Result of the file creation.
     */
    private function createFile(string $diskName, array $validatedData): array
    {
        $result = Storage::disk($diskName)->put($validatedData["path"], "");
        $message = $result ? "File created successfully" : "Failed to create file";

        return $this->getResponse($diskName, $result, $message, $validatedData["dirPath"]);
    }

    /**
     * Create a directory and return the result.
     *
     * @param string $diskName The disk name.
     * @param array $validatedData Validated data for directory creation.
     *
     * @return array Result of the directory creation.
     */
    private function createDir(string $diskName, array $validatedData): array
    {
        $result = Storage::disk($diskName)->makeDirectory($validatedData["path"]);
        $message = $result ? "Directory created successfully" : "Failed to create directory";

        return $this->getResponse($diskName, $result, $message, $validatedData["dirPath"]);
    }

    /**
     * Get the response array.
     *
     * @param string $diskName
     * @param bool $result The operation result.
     * @param string $message The result message.
     * @param string $dirPath The directory path.
     *
     * @return array Response array.
     */
    private function getResponse(string $diskName, bool $result, string $message, string $dirPath): array
    {
        list($dirs, $items) = $this->getRefreshedDiskData($diskName, $dirPath);

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
     * Get items within a directory.
     *
     * @param string $diskName
     * @param string $dirName
     *
     * @return array Items within the specified directory.
     */
    private function getRefreshedDiskData(string $diskName, string $dirName): array
    {
        $dirService = new DirService();

        return array($dirService->getDiskDirsForTree($diskName), $dirService->getDirItems($diskName, $dirName));
    }

    /**
     * Check if the directory is the default directory on loading.
     *
     * @param string|null $defaultDirOnLoading The default directory on loading.
     * @param string      $dirName             The directory name to check.
     *
     * @return bool
     */
    private function isDefaultDirectory(?string $defaultDirOnLoading, string $dirName): bool
    {
        return $defaultDirOnLoading && $defaultDirOnLoading == $dirName;
    }

    /**
     * Return a response for an invalid type.
     *
     * @return array Response for an invalid type.
     */
    private function invalidTypeResponse(): array
    {
        return [
            "result" => [
                'status' => "failed",
                'message' => "Invalid type",
                'items' => []
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
}
