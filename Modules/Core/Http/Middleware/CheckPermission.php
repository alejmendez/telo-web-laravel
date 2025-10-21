<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Core\Exceptions\PermissionException;
use Modules\Core\Services\Contracts\CacheServiceContract;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    private CacheServiceContract $cacheService;

    public function __construct(CacheServiceContract $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()->getName();
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $userPermissions = $this->cacheService->getUserPermissions($user);

        if (!$userPermissions->contains($routeName)) {
            throw new PermissionException($routeName);
        }

        return $next($request);
    }
}
