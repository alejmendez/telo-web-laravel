<?php

namespace Modules\Core\Traits;

use Modules\Core\Exceptions\PermissionException;
use Modules\Core\Services\Contracts\CacheServiceContract;

trait HasPermissionMiddleware
{
    protected function setupPermissionMiddleware()
    {
        $this->middleware(function ($request, $next) {
            $routeName = $request->route()->getName();
            $user = auth()->user();

            $cacheService = app()->make(CacheServiceContract::class);
            $userPermissions = $cacheService->getUserPermissions($user);

            if (! $userPermissions->contains($routeName)) {
                throw new PermissionException($routeName);
            }

            return $next($request);
        });
    }
}
