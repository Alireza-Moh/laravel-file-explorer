<?php

namespace AlirezaMoh\LaravelFileExplorer\Middleware;

use AlirezaMoh\LaravelFileExplorer\Supports\ConfigRepository;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateDisk
{
    public function handle(Request $request, Closure $next)
    {
        $disks = ConfigRepository::getDisks();
        $diskName = $request->route('diskName');

        return in_array($diskName, $disks) ? $next($request) : $this->denyAccess($diskName);
    }

    private function denyAccess(string $diskName): JsonResponse
    {
        return response()->json([
            "message" => "Invalid data sent",
            "errors" => [
                [
                    "diskName" => "Disk '$diskName' does not exist"
                ]
            ]
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
