<?php

namespace AlirezaMoh\LaravelFileExplorer\Http\Middleware;

use AlirezaMoh\LaravelFileExplorer\Supports\ApiResponse;
use AlirezaMoh\LaravelFileExplorer\Supports\ConfigRepository;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        return ApiResponse::error('Disk '. $diskName . ' does not exist');
    }
}
