<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Modules\Core\Services\Contracts\CacheServiceContract;
use Modules\Core\Services\Contracts\MenuServiceContract;

class HandleInertiaRequests extends Middleware
{
    private CacheServiceContract $cacheService;

    private MenuServiceContract $menuService;

    public function __construct(CacheServiceContract $cacheService, MenuServiceContract $menuService)
    {
        $this->cacheService = $cacheService;
        $this->menuService = $menuService;
    }
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'core::app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $userData = $user;

        if ($user) {
            $userData = $this->cacheService->getUserDataSession($user);
        }

        $menu = $this->menuService->getMenu($request, $user);

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $userData,
            ],
            'menu' => $menu,
        ];
    }
}
