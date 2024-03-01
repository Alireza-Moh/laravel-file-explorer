<?php

namespace Alireza\LaravelFileExplorer\Services;

use Alireza\LaravelFileExplorer\Events\ItemDeleted;

abstract class BaseItemManager
{
    protected const CARBON_TIME_FORMAT = "Y-m-d H:i:s";

    /**
     * Get the response based on the operation result
     *
     * @param bool   $result
     * @param string $success
     * @param string $failure
     * @param array  $additionalData
     * @return array
     */
    protected function getResponse(bool $result, string $success = "", string $failure = "", array $additionalData = []): array
    {
        $status = $result ? 'success' : 'failed';
        $message = $result ? $success : $failure;
        $response = [
            'result' => [
                'status'  => $status,
                'message' => $message,
            ],
        ];

        if (!empty($additionalData)) {
            $response['result'] += $additionalData;
        }

        return $response;
    }

    /**
     * Get the response array.
     *
     * @param string $diskName
     * @param bool $result
     * @param string $message
     * @param string $destination
     * @return array
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

    protected function fireDeleteEvent(string $diskName, array $item): void
    {
        event(new ItemDeleted($diskName, $item["name"], $item["path"]));
    }
}
