<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Core\Services\CacheService;
use Modules\Core\Services\Contracts\CacheServiceContract;
use Modules\Users\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SyncPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync permissions';

    protected $defaultGuard = 'web';

    protected $entities = [
        'users',
        'locations',
        'contacttypes',
        'professionaltypes',
        'customertypes',
        'categories',
    ];

    protected $defaultActions = [
        'index',
        'create',
        'store',
        'show',
        'edit',
        'update',
        'destroy',
    ];

    protected $permissions = [];

    protected $roles = [];

    protected PermissionRegistrar $permissionRegistrar;

    protected CacheServiceContract $cacheService;

    public function __construct(PermissionRegistrar $permissionRegistrar, CacheServiceContract $cacheService)
    {
        parent::__construct();
        $this->permissionRegistrar = $permissionRegistrar;
        $this->cacheService = $cacheService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->permissionRegistrar->forgetCachedPermissions();

        $this->create_roles();
        $this->clear_permissions();

        foreach ($this->entities as $entity) {
            foreach ($this->defaultActions as $action) {
                $this->create_permission($entity, $action);
            }
        }

        $this->create_permission('dashboard', 'index');
        $this->create_permission('selects', 'index');
        $this->create_permission('selects', 'multiple');

        $this->save_permissions();

        $this->permissions = collect($this->permissions);

        $allPermissions = $this->permissions->flatten();

        $this->roles['super_admin']->syncPermissions($allPermissions->toArray());
        $this->roles['administrator']->syncPermissions($allPermissions->toArray());

        $users = User::all();
        foreach ($users as $user) {
            $this->cacheService->clearUserCache($user);
        }

        return self::SUCCESS;
    }

    public function create_roles()
    {
        $this->roles = [
            'administrator' => 'Administrador',
            'super_admin' => 'Super Admin',
        ];

        foreach ($this->roles as $key => $name) {
            $rol = Role::where('name', $name)->first();
            if (! $rol) {
                $rol = Role::create(['name' => $name]);
            }
            $this->roles[$key] = $rol;
        }
    }

    public function create_permission($entity, $action)
    {
        $permission = $entity.'.'.$action;

        if (! isset($this->permissions[$entity])) {
            $this->permissions[$entity] = [];
        }

        $this->permissions[$entity][] = $permission;
    }

    public function save_permissions()
    {
        $existingPermissions = Permission::pluck('name')->toArray();
        $permissionToCreate = [];
        $now = now()->toDateTimeString();
        foreach ($this->permissions as $entity => $permissions) {
            foreach ($permissions as $permission) {
                if (! in_array($permission, $existingPermissions)) {
                    $permissionToCreate[] = [
                        'name' => $permission,
                        'guard_name' => $this->defaultGuard,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        if (count($permissionToCreate) > 0) {
            Permission::insert($permissionToCreate);
        }
    }

    public function clear_permissions()
    {
        $this->permissions = [];
        Permission::truncate();
        DB::table('role_has_permissions')->truncate();
    }
}
