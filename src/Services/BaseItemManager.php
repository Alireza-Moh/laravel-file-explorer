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
        $dirService = new DirService();

        return $this->getResponse(
            $result,
            $message,
            $message,
            [
                "items" => $dirService->getDirItems($diskName, $destination),
                "dirs" => $dirService->getDiskDirsForTree($diskName)
            ]
        );
    }
}
