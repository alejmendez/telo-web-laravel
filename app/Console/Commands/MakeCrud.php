<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MakeCrud extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud {module : The name of the module} {resource : The name of the resource (singular)} {resource_plural? : The name of the resource (plural)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a CRUD structure for a specific module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $module = Str::studly($this->argument('module'));
        $resource = Str::studly($this->argument('resource'));
        $resourcePlural = $this->argument('resource_plural') ? Str::studly($this->argument('resource_plural')) : Str::plural($resource);

        $this->info("Generating CRUD for {$resource} in module {$module}...");

        if (!is_dir(base_path("Modules/{$module}"))) {
            if ($this->confirm("Module {$module} does not exist. Do you want to create it?")) {
                $this->call('modules:make', ['module' => $module]);
            } else {
                $this->error("Module {$module} does not exist.");
                return;
            }
        }

        $placeholders = [
            '{{module}}' => $module,
            '{{module_lower}}' => Str::lower($module),
            '{{resource}}' => $resource,
            '{{resource_plural}}' => $resourcePlural,
            '{{resource_lower}}' => Str::lower($resource),
            '{{resource_plural_lower}}' => Str::lower($resourcePlural),
            '{{resource_camel}}' => Str::camel($resource),
            '{{resource_plural_camel}}' => Str::camel($resourcePlural),
        ];

        $this->createController($module, $resource, $placeholders);
        $this->createRequests($module, $resource, $placeholders);
        $this->createResource($module, $resource, $placeholders);
        $this->createServicePHP($module, $resource, $placeholders);
        $this->createServiceJS($module, $resource, $placeholders);
        $this->createViews($module, $resource, $resourcePlural, $placeholders);
        $this->updateRoutes($module, $resource, $resourcePlural, $placeholders);

        // Service Provider check (usually created by make:module, but we can verify)
        $this->checkServiceProvider($module, $placeholders);

        $this->info("CRUD for {$resource} generated successfully!");
        $this->comment("Remember to register translations and permissions if needed.");
    }

    protected function getStub($name)
    {
        return file_get_contents(__DIR__ . "/Stubs/Crud/{$name}.stub");
    }

    protected function createFile($path, $stubName, $placeholders)
    {
        if (File::exists($path)) {
            $this->warn("File already exists: {$path}");
            return;
        }

        $content = $this->getStub($stubName);

        foreach ($placeholders as $key => $value) {
            $content = str_replace($key, $value, $content);
        }

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);
        $this->info("Created: {$path}");
    }

    protected function createController($module, $resource, $placeholders)
    {
        $path = base_path("Modules/{$module}/Http/Controllers/{$placeholders['{{resource_plural}}']}Controller.php");
        $this->createFile($path, 'Controller', $placeholders);
    }

    protected function createRequests($module, $resource, $placeholders)
    {
        $pathStore = base_path("Modules/{$module}/Http/Requests/Store{$resource}Request.php");
        $this->createFile($pathStore, 'StoreRequest', $placeholders);

        $pathUpdate = base_path("Modules/{$module}/Http/Requests/Update{$resource}Request.php");
        $this->createFile($pathUpdate, 'UpdateRequest', $placeholders);
    }

    protected function createResource($module, $resource, $placeholders)
    {
        $path = base_path("Modules/{$module}/Http/Resources/{$resource}Resource.php");
        $this->createFile($path, 'Resource', $placeholders);
    }

    protected function createModel($module, $resource, $placeholders)
    {
        $path = base_path("Modules/{$module}/Models/{$resource}.php");
        $this->createFile($path, 'Model', $placeholders);
    }

    protected function createServicePHP($module, $resource, $placeholders)
    {
        $path = base_path("Modules/{$module}/Services/{$resource}Service.php");
        $this->createFile($path, 'Service', $placeholders);
    }

    protected function createServiceJS($module, $resource, $placeholders)
    {
        $path = base_path("Modules/{$module}/Resources/Services/{$resource}Service.js");
        $this->createFile($path, 'ServiceJS', $placeholders);
    }

    protected function createViews($module, $resource, $resourcePlural, $placeholders)
    {
        $viewsPath = base_path("Modules/{$module}/Resources/Pages/{$resourcePlural}");

        $files = [
            'List.vue' => 'Pages/List.vue',
            'Create.vue' => 'Pages/Create.vue',
            'Edit.vue' => 'Pages/Edit.vue',
            'Show.vue' => 'Pages/Show.vue',
            'Form.vue' => 'Pages/Form.vue',
        ];

        foreach ($files as $file => $stub) {
            $this->createFile("{$viewsPath}/{$file}", $stub, $placeholders);
        }
    }

    protected function updateRoutes($module, $resource, $resourcePlural, $placeholders)
    {
        $routesPath = base_path("Modules/{$module}/Routes/web.php");

        if (!File::exists($routesPath)) {
            $this->warn("Routes file not found: {$routesPath}");
            return;
        }

        $routesContent = File::get($routesPath);
        $routeDefinition = "        Route::resources([\n            '{$placeholders['{{resource_plural_lower}}']}' => {$resourcePlural}Controller::class,\n        ]);";
        $useStatement = "use Modules\\{$module}\\Http\\Controllers\\{$resourcePlural}Controller;";

        if (strpos($routesContent, $resourcePlural . 'Controller::class') !== false) {
            $this->warn("Route for {$resourcePlural} seems to exist already.");
            return;
        }

        // Add Use statement
        if (strpos($routesContent, $useStatement) === false) {
             // Find the namespace or open tag and add use statement after it
             // Or just after the first use statement
             $pattern = '/^use .*;/m';
             if (preg_match($pattern, $routesContent, $matches)) {
                 $routesContent = preg_replace($pattern, "$0\n{$useStatement}", $routesContent, 1);
             } else {
                 $routesContent = str_replace("<?php", "<?php\n\n{$useStatement}", $routesContent);
             }
        }

        // Add Route inside middleware group
        if (strpos($routesContent, 'Route::resources([') !== false) {
            // Add to existing array
             $routesContent = preg_replace(
                "/Route::resources\(\[\s*/",
                "Route::resources([\n            '{$placeholders['{{resource_plural_lower}}']}' => {$resourcePlural}Controller::class,\n            ",
                $routesContent,
                1
            );
        } else {
             // Try to find the inner auth group
             $target = "Route::middleware(['auth', 'check.permission'])->group(function () {";
             if (strpos($routesContent, $target) !== false) {
                 $routesContent = str_replace(
                     $target,
                     "{$target}\n{$routeDefinition}",
                     $routesContent
                 );
             } else {
                 // Fallback
                 $routesContent .= "\n\n" . $routeDefinition;
             }
        }

        File::put($routesPath, $routesContent);
        $this->info("Routes updated in: {$routesPath}");
    }

    protected function checkServiceProvider($module, $placeholders)
    {
        $path = base_path("Modules/{$module}/Providers/{$module}ServiceProvider.php");
        if (!File::exists($path)) {
             $this->createFile($path, 'ServiceProvider', $placeholders);
        }
    }
}
