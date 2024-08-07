<?php

namespace AlirezaMoh\LaravelFileExplorer\Http\Middleware;

use AlirezaMoh\LaravelFileExplorer\Supports\ApiResponse;
use AlirezaMoh\LaravelFileExplorer\Supports\ConfigRepository;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidatePermission
{
    public function handle(Request $request, Closure $next, string $requiredPermission)
    {
        if (ConfigRepository::isACLEnabled()) {
            $user = $request->user();

            if (is_null($user)) {
                return ApiResponse::error('Could not validate user permission');
            }
            return ($user && $user->hasPermission($requiredPermission))
                ? $next($request)
                : $this->denyAccess();
        }
        return $next($request);
    }

    private function denyAccess(): Response
    {
        return ApiResponse::forbidden('You dont have the necessary permission for this action');
    }
}
