<?php

namespace Alireza\LaravelFileExplorer\Services;

abstract class BaseItemManager
{
    /**
     * Get the response based on the operation result
     *
     * @param bool   $result  The result of the operation.
     * @param string $success The success message.
     * @param string $failure The failure message.
     * @param array  $customData additional data
     *
     * @return array The response
     */
    protected function getResponse(bool $result, string $success = "", string $failure = "", array $customData = []): array
    {
        $status = $result ? 'success' : 'failed';
        $message = $result ? $success : $failure;
        $response = [
            'result' => [
                'status'  => $status,
                'message' => $message,
            ],
        ];

        if (!empty($customData)) {
            $response['result'] += $customData;
        }

        return $response;
    }

    /**
     * Get the response array.
     *
     * @param string $diskName
     * @param bool $result The operation result.
     * @param string $message The result message.
     * @param string $destination The directory path.
     *
     * @return array Response array.
     */
    protected function getCreationResponse(string $diskName, bool $result, string $message, string $destination): array
    {
        list($dirs, $items) = $this->getRefreshedDiskData($diskName, $destination);

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
     * @param string $destination Directory path
     * @return array Items within the specified directory.
     */
    private function getRefreshedDiskData(string $diskName, string $destination): array
    {
        $dirService = new DirService();

        return array($dirService->getDiskDirs($diskName), $dirService->getDirItems($diskName, $destination));
    }
}
