<?php

namespace AlirezaMoh\LaravelFileExplorer\Supports\Traits;

use AlirezaMoh\LaravelFileExplorer\Exceptions\ZipperException;
use AlirezaMoh\LaravelFileExplorer\Supports\Zipper;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

trait DownloadManager
{
    /**
     * Sorts items by type into files and directories
     *
     * @return array
     */
    private function sortByType(): array
    {
        $items = [];
        $dirs = [];

        foreach ($this->items as $item) {
            if ($item['type'] === 'dir') {
                $dirs[] = $item;
            } elseif ($item['type'] === 'file') {
                $items[] = $item;
            }
        }

        return array($items, $dirs);
    }

    /**
     * Zip all items
     *
     * @return JsonResponse|BinaryFileResponse
     */
    private function zipItems(): JsonResponse|BinaryFileResponse
    {
        $zipName = $this->diskName . ".zip";

        try {
            $zipper = new Zipper($this->diskName, $zipName, $this->items);
            $zipper->zip();
            return Response::download($zipper->getZipPath())->deleteFileAfterSend();
        } catch (ZipperException $zipperException) {
            return $this->throwZipperException($zipperException);
        }
    }

    /**
     * Return a json response with the exception message
     *
     * @param ZipperException $zipperException
     * @return JsonResponse
     */
    public function throwZipperException(ZipperException $zipperException): JsonResponse
    {
        return response()->json(
            [
                "result" => [
                    [
                        "status" => "failed",
                        "message" => $zipperException->getMessage()
                    ]
                ]
            ]
        );
    }
}
