<?php

namespace AlirezaMoh\LaravelFileExplorer\Http\Middleware;

use AlirezaMoh\LaravelFileExplorer\Exceptions\NullUserException;
use AlirezaMoh\LaravelFileExplorer\Supports\ApiResponse;
use AlirezaMoh\LaravelFileExplorer\Supports\ConfigRepository;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidatePermission
{
    /**
     * @throws NullUserException
     */
    public function handle(Request $request, Closure $next, string $requiredPermission)
    {
        if (ConfigRepository::isACLEnabled()) {
            $user = $request->user();

            if (is_null($user)) {
                throw new NullUserException();
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
