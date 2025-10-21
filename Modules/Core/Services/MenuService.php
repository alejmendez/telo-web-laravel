<?php

namespace Modules\Core\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Modules\Core\Services\Contracts\CacheServiceContract;
use Modules\Core\Services\Contracts\MenuServiceContract;
use Modules\Core\Models\Menu;
use Modules\Core\Models\Module;
use Modules\Users\Models\User;

/**
 * Service for handling menu generation and management in the application
 */
class MenuService implements MenuServiceContract
{
    private const CACHE_TTL_MODULES = 600; // 10 minutes

    private const CACHE_TTL_MENUS = 600; // 10 minutes

    private const CACHE_KEY_MODULES = 'modules';

    private const CACHE_KEY_MENUS = 'menus';

    private CacheServiceContract $cacheService;

    public function __construct(CacheServiceContract $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Gets the complete menu for the current user
     */
    public function getMenu(Request $request, ?User $user = null): array
    {
        if (! $user) {
            return [];
        }

        $permissions = $this->cacheService->getUserPermissions($user)->toArray();
        $modules = $this->getModules();
        $menus = $this->getMenus();

        $rootElements = $menus->whereNull('parent_id');

        return $rootElements
            ->map(fn ($item) => $this->getMenuData($item, $request, $permissions, $modules, $menus))
            ->filter()
            ->values()
            ->toArray();
    }

    /**
     * Gets the data for a menu item
     */
    private function getMenuData(Menu $item, Request $request, array $permissions, Collection $modules, Collection $menus): ?array
    {
        $children = $menus->where('parent_id', $item->id);

        if ($children->isEmpty()) {
            return $this->getLeafMenuItem($item, $request, $permissions, $modules);
        }

        return $this->getParentMenuItem($item, $children, $request, $permissions, $modules, $menus);
    }

    /**
     * Gets the data for a leaf menu item
     */
    private function getLeafMenuItem(Menu $item, Request $request, array $permissions, Collection $modules): ?array
    {
        if (! $modules->has($item->module_id) || ! in_array($item->link, $permissions)) {
            return null;
        }

        return [
            'text' => __($item->text),
            'link' => route($item->link),
            'icon' => $item->icon,
            'active' => $request->routeIs($item->active_with),
        ];
    }

    /**
     * Gets the data for a parent menu item
     */
    private function getParentMenuItem(Menu $item, Collection $children, Request $request, array $permissions, Collection $modules, Collection $menus): ?array
    {
        $childrenData = $children->map(fn ($child) => $this->getMenuData($child, $request, $permissions, $modules, $menus))
            ->filter()
            ->values();

        if ($childrenData->isEmpty()) {
            return null;
        }

        return [
            'text' => __($item->text),
            'children' => $childrenData,
        ];
    }

    /**
     * Gets active modules from cache
     */
    private function getModules(): Collection
    {
        return cache()->remember(
            self::CACHE_KEY_MODULES,
            self::CACHE_TTL_MODULES,
            fn () => Module::where('is_active', true)->pluck('name', 'id')
        );
    }

    /**
     * Gets menu items from cache
     */
    private function getMenus(): Collection
    {
        return cache()->remember(
            self::CACHE_KEY_MENUS,
            self::CACHE_TTL_MENUS,
            fn () => Menu::orderBy('order')->get()
        );
    }

    /**
     * Gets root menu elements
     */
    private function getRootElements(): Collection
    {
        return $this->menus->whereNull('parent_id');
    }
}
