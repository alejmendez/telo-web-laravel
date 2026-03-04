# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

### Development
```bash
composer run dev          # Starts server (port 9000), queue listener, and Vite concurrently
npm run dev               # Vite dev server only
php artisan serve --port=9000
```

### Build & Assets
```bash
npm run build             # Production Vite build
npm run format            # Biome format JS/Vue files in resources/js and Modules/
```

### Linting & Formatting
```bash
./vendor/bin/pint         # PHP code style (Laravel preset)
./vendor/bin/duster lint  # Combined PHP linting
./vendor/bin/duster fix   # Combined PHP auto-fix
```

### Testing
```bash
composer run test         # Clears config then runs all tests
php artisan test          # Run all tests
php artisan test --filter=TestClassName  # Run single test
php artisan test tests/Feature/SomeTest.php  # Run specific file
```

### Useful Artisan
```bash
php artisan ziggy:generate --url=https://telochile.cl/  # Regenerate Ziggy routes (see resources/js/ziggy.js)
php artisan config:clear
php artisan cache:clear
```

## Architecture

### Module System
The app uses a custom module system under `Modules/`. Each module is a self-contained bounded context:

- **Core** – base classes, middleware, services (PrimevueDatatables, MenuService, CacheService), shared Vue components, layouts, utilities, stores
- **Auth** – authentication pages and permission helper (`can()`)
- **Dashboard** – dashboard pages
- **Crm** – main business domain (see domain model below)
- **Users** – user management
- **Website** – public-facing website (separate from backoffice)

Each module follows this structure:
```
Modules/{Name}/
  Database/Migrations/
  Http/Controllers/    # Inertia controllers
  Http/Requests/       # Form request validation
  Http/Resources/      # API resources
  Lang/                # Translation files (JSON + PHP)
  Models/
  Providers/{Name}ServiceProvider.php  # Registers routes, migrations, translations
  Resources/
    Components/        # Vue components (module-specific)
    Lang/              # Frontend i18n
    Layouts/
    Pages/             # Inertia page components
    Services/          # JS service layer (axios calls)
    Stores/            # Pinia stores
    Utils/             # JS utility functions
  Routes/web.php
  Services/            # PHP service classes
```

### Backend Patterns

**Controller pattern** – Controllers extend `Modules\Core\Http\Controllers\Controller` and use `HasPermissionMiddleware` trait. Permission names match route names (e.g., `requests.index`, `requests.store`). The `index` method checks for `dt_params` query param to serve datatable JSON:

```php
public function index() {
    if (request()->exists('dt_params')) {
        return response()->json($this->service->list(json_decode(request('dt_params', '[]'), true)));
    }
    return Inertia::render('Module::Entity/List', [...]);
}
```

**Service pattern** – PHP services handle all business logic. They use `PrimevueDatatables` for server-side pagination/filtering/sorting. The `list()` method accepts `$params` from PrimeVue's DataTable lazy event format. `listAsSelect()` returns `[['value' => id, 'text' => label], ...]` for dropdowns.

**`query_to_select()` helper** – Global helper in `app/Helpers/crud_helpers.php` that converts a query builder to a select-compatible array with optional filtering.

**Permissions** – Stored in cache via `CacheService`. Permission names are route names. The `check.permission` middleware enforces this; `HasPermissionMiddleware` does it at controller level.

**Inertia pages** – Resolved via module notation: `Inertia::render('Crm::Requests/List')` maps to `Modules/Crm/Resources/Pages/Requests/List.vue`.

### Frontend Patterns

**Inertia + Vue 3** with Composition API (`<script setup>`). All pages are in `Modules/{Name}/Resources/Pages/`.

**Module aliases** – Vite aliases map `@Core`, `@Auth`, `@Dashboard`, `@Crm`, `@Users` to each module's `Resources/` directory.

**PrimeVue** is the UI component library. `Datatable.vue` in Core wraps PrimeVue's DataTable with lazy loading, column toggling, and server-side filtering built in.

**Datatable flow:**
1. Vue page defines `filters` object and `columns` array
2. `fetchHandler` calls the module's JS service (e.g., `RequestsService.list(params)`)
3. JS service calls `Datatable.list(route('entity.index'), lazyParams)` which sends `dt_params` as JSON
4. Backend controller detects `dt_params`, passes to PHP service's `list()`, returns paginated JSON

**Permission checking in Vue** – `import { can } from '@Auth/Services/Auth'` then `can('requests.show')`.

**i18n** – `laravel-vue-i18n` with `trans()` / `__()` in templates. Translation files in each module's `Lang/` directory. Backend uses Laravel's `__()`.

**JS formatting** – Biome with single quotes, 2-space indent, 140 char line width, trailing commas, semicolons required.

### CRM Domain Model
The CRM module (primary business domain) models a service-request marketplace:
- **Customers** request services → **Requests** (pending/active/rejected)
- **Requests** are assigned to **Professionals** → become **Services**
- **Services** are rated via **ServiceRatings**
- **Professionals** have **Subscriptions** tied to **SubscriptionPlans**
- Location hierarchy: Country → City → Commune; addresses are historical (effective_from/effective_to)
- A customer can only have one active/pending request at a time (enforced by partial unique index)

### Routes
All backoffice routes are prefixed with `/backoffice` and protected by `auth` + `check.permission` middleware. Each module's `ServiceProvider` registers its own routes. The public website is under `Modules/Website`.

### Code Style
- PHP: Laravel Pint (Laravel preset). Always use curly braces for control structures.
- JS/Vue: Biome formatter (`npm run format`).
- New modules/controllers follow the patterns in `Modules/Crm/` as the canonical reference.

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.5.1
- inertiajs/inertia-laravel (INERTIA_LARAVEL) - v2
- laravel/framework (LARAVEL) - v12
- laravel/octane (OCTANE) - v2
- laravel/prompts (PROMPTS) - v0
- laravel/sanctum (SANCTUM) - v4
- tightenco/ziggy (ZIGGY) - v2
- laravel/boost (BOOST) - v2
- laravel/breeze (BREEZE) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- phpunit/phpunit (PHPUNIT) - v12
- @inertiajs/vue3 (INERTIA_VUE) - v2
- vue (VUE) - v3
- tailwindcss (TAILWINDCSS) - v4

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan

- Use the `list-artisan-commands` tool when you need to call an Artisan command to double-check the available parameters.

## URLs

- Whenever you share a project URL with the user, you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain/IP, and port.

## Tinker / Debugging

- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.
- Use the `database-schema` tool to inspect table structure before writing migrations or models.

## Reading Browser Logs With the `browser-logs` Tool

- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)

- Boost comes with a powerful `search-docs` tool you should use before trying other approaches when working with Laravel or Laravel ecosystem packages. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic-based queries at once. For example: `['rate limiting', 'routing rate limiting', 'routing']`. The most relevant results will be returned first.
- Do not add package names to queries; package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'.
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit".
3. Quoted Phrases (Exact Position) - query="infinite scroll" - words must be adjacent and in that order.
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit".
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms.

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.

## Constructors

- Use PHP 8 constructor property promotion in `__construct()`.
    - `public function __construct(public GitHub $github) { }`
- Do not allow empty `__construct()` methods with zero parameters unless the constructor is private.

## Type Declarations

- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<!-- Explicit Return Types and Method Params -->
```php
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
```

## Enums

- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.

## Comments

- Prefer PHPDoc blocks over inline comments. Never use comments within the code itself unless the logic is exceptionally complex.

## PHPDoc Blocks

- Add useful array shape type definitions when appropriate.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== inertia-laravel/core rules ===

# Inertia

- Inertia creates fully client-side rendered SPAs without modern SPA complexity, leveraging existing server-side patterns.
- Components live in `resources/js/Pages` (unless specified in `vite.config.js`). Use `Inertia::render()` for server-side routing instead of Blade views.
- ALWAYS use `search-docs` tool for version-specific Inertia documentation and updated code examples.
- IMPORTANT: Activate `inertia-vue-development` when working with Inertia Vue client-side patterns.

# Inertia v2

- Use all Inertia features from v1 and v2. Check the documentation before making changes to ensure the correct approach.
- New features: deferred props, infinite scrolling (merging props + `WhenVisible`), lazy loading on scroll, polling, prefetching.
- When using deferred props, add an empty state with a pulsing or animated skeleton.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

## Database

- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries.
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## Controllers & Validation

- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

## Authentication & Authorization

- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Queues

- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

## Configuration

- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== laravel/v12 rules ===

# Laravel 12

- CRITICAL: ALWAYS use `search-docs` tool for version-specific Laravel documentation and updated code examples.
- This project upgraded from Laravel 10 without migrating to the new streamlined Laravel file structure.
- This is perfectly fine and recommended by Laravel. Follow the existing structure from Laravel 10. We do not need to migrate to the new Laravel structure unless the user explicitly requests it.

## Laravel 10 Structure

- Middleware typically lives in `app/Http/Middleware/` and service providers in `app/Providers/`.
- There is no `bootstrap/app.php` application configuration in a Laravel 10 structure:
    - Middleware registration happens in `app/Http/Kernel.php`
    - Exception handling is in `app/Exceptions/Handler.php`
    - Console commands and schedule register in `app/Console/Kernel.php`
    - Rate limits likely exist in `RouteServiceProvider` or `app/Http/Kernel.php`

## Database

- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 12 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models

- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should cover all happy paths, failure paths, and edge cases.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

## Running Tests

- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test --compact`.
- To run all tests in a file: `php artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --compact --filter=testName` (recommended after making a change to a related file).

=== inertia-vue/core rules ===

# Inertia + Vue

Vue components must have a single root element.
- IMPORTANT: Activate `inertia-vue-development` when working with Inertia Vue client-side patterns.

=== tailwindcss/core rules ===

# Tailwind CSS

- Always use existing Tailwind conventions; check project patterns before adding new ones.
- IMPORTANT: Always use `search-docs` tool for version-specific Tailwind CSS documentation and updated code examples. Never rely on training data.
- IMPORTANT: Activate `tailwindcss-development` every time you're working with a Tailwind CSS or styling-related task.

=== tightenco/duster rules ===

## Duster Code Formatter

- You must run `vendor/bin/duster fix --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Duster wraps Laravel Pint and other formatters, so never run Pint directly. Always prefer Duster for formatting tasks.

</laravel-boost-guidelines>
