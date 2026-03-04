---
name: inertia-crud-generator
description: Use when creating a new CRUD resource in this Laravel + Inertia project. Activates when user says "crea un crud", "genera un crud", "new crud", "add entity", or when creating any new module entity from a migration file. Covers all layers: migration reading, PHP service, controller, form requests, Eloquent resource, model, routes, service provider, JS service, lang file, and Vue pages.
---

# Inertia CRUD Generator

## Overview

Generates a complete CRUD for the project following the exact patterns from `Modules/Crm/`. The canonical reference is the `Categories` CRUD (simplest full example). Always read the migration first to derive all field names, types, and constraints before generating any file.

## Step 1: Read and Parse the Migration

Given a migration file path, extract:
- **Table name** (from `Schema::create('table_name', ...)`) → derive entity names
- **Columns**: name, type, nullable, unique, foreign key references
- **Skip**: `id`, `timestamps`, `softDeletes`

**Name derivation from table `categories`:**
| Convention | Value |
|---|---|
| Entity singular PascalCase | `Category` |
| Entity plural camelCase | `categories` |
| Controller | `CategoriesController` |
| PHP Service | `CategoryService` |
| Model | `Category` |
| Resource | `CategoryResource` |
| Route name prefix | `categories` |
| JS Service | `CategoryService.js` |
| Lang file | `category.php` |
| Lang key prefix | `category` |
| Inertia path prefix | `Crm::Categories/` |
| Vue alias import | `@Crm/Pages/Categories/` |

**Migration type → validation rule mapping:**
| Migration type | `required` rule | `nullable` rule |
|---|---|---|
| `string($col, 250)` | `required\|max:250` | `nullable\|max:250` |
| `string($col)` | `required\|max:255` | `nullable\|max:255` |
| `text($col)` | `required\|max:2000` | `nullable\|max:2000` |
| `integer` / `bigInteger` / `unsignedBigInteger` (FK) | `required\|integer\|exists:table,id` | `nullable\|integer\|exists:table,id` |
| `boolean` | `required\|boolean` | `nullable\|boolean` |
| `decimal` / `float` / `double` | `required\|numeric` | `nullable\|numeric` |
| `date` | `required\|date` | `nullable\|date` |
| `dateTime` / `timestamp` | `required\|date` | `nullable\|date` |
| `enum` (use PHP Enum) | `required\|in:...` | `nullable\|in:...` |
| `unique` modifier | add `\|unique:table,column` to Store, `\|unique:table,column,{$id}` to Update | — |

## Step 2: Generate Files (in order)

Generate ALL files below. Ask user for module name if not clear from context.

---

### 2.1 Model

**Path:** `Modules/{Module}/Models/{Entity}.php`

```php
<?php

namespace Modules\{Module}\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class {Entity} extends Model
{
    use HasFactory;

    protected $fillable = [/* all non-id, non-timestamp columns */];

    protected static function newFactory()
    {
        return \Modules\{Module}\Database\Factories\{Entity}Factory::new();
    }

    // Add belongsTo() for each FK column, e.g.:
    // public function location(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    // {
    //     return $this->belongsTo(Location::class);
    // }
}
```

---

### 2.2 PHP Service

**Path:** `Modules/{Module}/Services/{Entity}Service.php`

```php
<?php

namespace Modules\{Module}\Services;

use Modules\Core\Services\PrimevueDatatables;
use Modules\{Module}\Models\{Entity};

class {Entity}Service
{
    protected const SEARCHABLE_COLUMNS = [/* string columns suitable for search */];

    public function list(array $params = []): mixed
    {
        $query = {Entity}::query();
        // Add ->with([...]) if entity has relationships to eager-load

        $datatable = new PrimevueDatatables($params, self::SEARCHABLE_COLUMNS);

        return $datatable->of($query)->make();
    }

    public function listAsSelect(array $filter = []): mixed
    {
        return query_to_select(
            {Entity}::select('id as value', 'name as text')->orderBy('name'),
            ['id', 'name'],
            $filter
        );
    }

    public function find(int $id): {Entity}
    {
        return {Entity}::findOrFail($id);
    }

    public function create(array $data): {Entity}
    {
        $entity = new {Entity};
        // $entity->field = $data['field'];
        $entity->save();

        return $entity;
    }

    public function update(int $id, array $data): {Entity}
    {
        $entity = $this->find($id);
        // $entity->field = $data['field'] ?? $entity->field;
        $entity->save();

        return $entity;
    }

    public function delete(int $id): bool
    {
        return $this->find($id)->delete();
    }
}
```

---

### 2.3 StoreRequest

**Path:** `Modules/{Module}/Http/Requests/{Entity}/StoreRequest.php`

```php
<?php

namespace Modules\{Module}\Http\Requests\{Entity};

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 'field' => 'required|max:250',
        ];
    }

    public function attributes(): array
    {
        return [
            // 'field' => __('{entity}.form.field.label'),
        ];
    }

    // Add prepareForValidation() if PrimeVue sends objects for selects:
    // protected function prepareForValidation(): void
    // {
    //     $this->merge([
    //         'fk_id' => $this->fk_id['value'] ?? null,
    //     ]);
    // }
}
```

---

### 2.4 UpdateRequest

**Path:** `Modules/{Module}/Http/Requests/{Entity}/UpdateRequest.php`

Same as StoreRequest but:
- Unique rules become: `unique:table,column,{$this->route('entity')}`
- Fields can be `sometimes|required` if partial updates are needed

```php
<?php

namespace Modules\{Module}\Http\Requests\{Entity};

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('{routeParam}'); // e.g. 'category'

        return [
            // 'name' => "required|max:250|unique:table,name,{$id}",
        ];
    }

    public function attributes(): array
    {
        return [
            // 'field' => __('{entity}.form.field.label'),
        ];
    }
}
```

---

### 2.5 Eloquent Resource

**Path:** `Modules/{Module}/Http/Resources/{Entity}Resource.php`

```php
<?php

namespace Modules\{Module}\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class {Entity}Resource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            // list all columns explicitly
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
```

---

### 2.6 Controller

**Path:** `Modules/{Module}/Http/Controllers/{PluralEntity}Controller.php`

```php
<?php

namespace Modules\{Module}\Http\Controllers;

use Inertia\Inertia;
use Modules\Core\Http\Controllers\Controller;
use Modules\Core\Traits\HasPermissionMiddleware;
use Modules\{Module}\Http\Requests\{Entity}\StoreRequest;
use Modules\{Module}\Http\Requests\{Entity}\UpdateRequest;
use Modules\{Module}\Http\Resources\{Entity}Resource;
use Modules\{Module}\Services\{Entity}Service;

class {PluralEntity}Controller extends Controller
{
    use HasPermissionMiddleware;

    public function __construct(protected {Entity}Service ${entityVar}Service)
    {
        $this->setupPermissionMiddleware();
    }

    public function index(): mixed
    {
        if (request()->exists('dt_params')) {
            $params = json_decode(request('dt_params', '[]'), true);

            return response()->json($this->{entityVar}Service->list($params));
        }

        return Inertia::render('{Module}::{PluralEntity}/List', [
            'toast' => session('toast'),
        ]);
    }

    public function create(): mixed
    {
        return Inertia::render('{Module}::{PluralEntity}/Create');
    }

    public function store(StoreRequest $request): mixed
    {
        $data = $request->validated();
        $this->{entityVar}Service->create($data);

        return redirect()->route('{routePrefix}.index')->with('toast', [
            'severity' => 'success',
            'summary' => __('generics.messages.saved_successfully'),
            'detail' => __('generics.messages.saved_successfully'),
            'life' => 5000,
        ]);
    }

    public function show(int $id): mixed
    {
        $entity = $this->{entityVar}Service->find($id);

        return Inertia::render('{Module}::{PluralEntity}/Show', [
            'data' => new {Entity}Resource($entity),
        ]);
    }

    public function edit(int $id): mixed
    {
        $entity = $this->{entityVar}Service->find($id);

        return Inertia::render('{Module}::{PluralEntity}/Edit', [
            'data' => new {Entity}Resource($entity),
        ]);
    }

    public function update(UpdateRequest $request, int $id): mixed
    {
        $data = $request->validated();
        $this->{entityVar}Service->update($id, $data);

        return redirect()->route('{routePrefix}.index')->with('toast', [
            'severity' => 'success',
            'summary' => __('generics.messages.saved_successfully'),
            'detail' => __('generics.messages.saved_successfully'),
            'life' => 5000,
        ]);
    }

    public function destroy(int $id): mixed
    {
        $this->{entityVar}Service->delete($id);

        return response()->noContent();
    }
}
```

---

### 2.7 Routes

**File:** `Modules/{Module}/Routes/web.php`

Add to the existing `Route::resources([...])` array:

```php
'{routePrefix}' => {PluralEntity}Controller::class,
```

Add the import at the top:
```php
use Modules\{Module}\Http\Controllers\{PluralEntity}Controller;
```

---

### 2.8 ServiceProvider

**File:** `Modules/{Module}/Providers/{Module}ServiceProvider.php`

Add the singleton registration to `boot()`:

```php
$this->app->singleton({Entity}Service::class, function (Application $app) {
    return new {Entity}Service;
});
```

Add the import:
```php
use Modules\{Module}\Services\{Entity}Service;
```

---

### 2.9 Lang File

**Path:** `Modules/{Module}/Lang/es/{entity}.php`

```php
<?php

return [
    'titles' => [
        'entity_breadcrumb' => '{PluralLabel}',
        'create' => 'Crear {Label}',
        'edit' => 'Editar {Label}',
        'show' => 'Detalle de {Label}',
    ],
    'table' => [
        // One entry per column shown in table:
        // 'name' => [
        //     'label' => 'Nombre',
        //     'placeholder' => 'Buscar por nombre',
        // ],
    ],
    'form' => [
        // One entry per form field:
        // 'name' => [
        //     'label' => 'Nombre',
        // ],
    ],
];
```

---

### 2.10 JS Service

**Path:** `Modules/{Module}/Resources/Services/{Entity}Service.js`

```js
import axios from 'axios';
import datatable from '@Core/Services/Datatable';

const list = async (lazyParams) => {
  const response = await datatable.list(route('{routePrefix}.index'), lazyParams);

  return response.data;
};

const del = async (id) => {
  await axios.delete(route('{routePrefix}.destroy', { id }));
  return true;
};

export default {
  list,
  del,
};
```

---

### 2.11 Vue Pages

**Form.vue** — `Modules/{Module}/Resources/Pages/{PluralEntity}/Form.vue`

```vue
<script setup>
import CardSection from '@Core/Components/CardSection.vue';
import VInput from '@Core/Components/Form/VInput.vue';
// import VSelect from '@Core/Components/Form/VSelect.vue';  // for FK selects
// import VTextarea from '@Core/Components/Form/VTextarea.vue';  // for text fields

const props = defineProps({
  form: Object,
  readOnly: Boolean,
  submitHandler: {
    type: Function,
    default: false,
  },
});

const form = props.form;
</script>

<template>
  <form @submit.prevent="props.submitHandler">
    <CardSection>
      <!-- VInput for each string/text field -->
      <!-- <VInput id="name" v-model="form.name" :label="__('{entity}.form.name.label')" :message="form.errors.name" :readonly="props.readOnly" /> -->
    </CardSection>
  </form>
</template>
```

**Create.vue** — `Modules/{Module}/Resources/Pages/{PluralEntity}/Create.vue`

```vue
<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import Form{Entity} from '@{Module}/Pages/{PluralEntity}/Form.vue';

const props = defineProps();

const form = useForm({
  // field: null,
});

const submitHandler = () => form.post(route('{routePrefix}.store'));
</script>

<template>
  <AuthenticatedLayout :title="__('{entity}.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('{entity}.titles.create')"
      :breadcrumbs="[{ to: '{routePrefix}.index', text: __('{entity}.titles.entity_breadcrumb') }, { text: __('generics.actions.create') }]"
      :form="{ instance: form, submitHandler, submitText: __('generics.buttons.create'), hrefCancel: route('{routePrefix}.index') }"
    />
    <Form{Entity} :form="form" :submitHandler="submitHandler" />
  </AuthenticatedLayout>
</template>
```

**Edit.vue** — `Modules/{Module}/Resources/Pages/{PluralEntity}/Edit.vue`

```vue
<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import Form{Entity} from '@{Module}/Pages/{PluralEntity}/Form.vue';

const props = defineProps({
  data: Object,
});

const { data } = props.data;

const form = useForm({
  _method: 'PATCH',
  id: data.id,
  // field: data.field,
});

const submitHandler = () => form.post(route('{routePrefix}.update', data.id));
</script>

<template>
  <AuthenticatedLayout :title="__('{entity}.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('{entity}.titles.edit')"
      :breadcrumbs="[{ to: '{routePrefix}.index', text: __('{entity}.titles.entity_breadcrumb') }, { text: __('generics.actions.edit') }]"
      :form="{ instance: form, submitHandler, submitText: __('generics.buttons.save_edit'), hrefCancel: route('{routePrefix}.index') }"
    />
    <Form{Entity} :form="form" :submitHandler="submitHandler" />
  </AuthenticatedLayout>
</template>
```

**Show.vue** — `Modules/{Module}/Resources/Pages/{PluralEntity}/Show.vue`

```vue
<script setup>
import { useForm } from '@inertiajs/vue3';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import Form{Entity} from '@{Module}/Pages/{PluralEntity}/Form.vue';

const props = defineProps({
  data: Object,
});

const { data } = props.data;

const form = useForm({
  id: data.id,
  // field: data.field,
});

const submitHandler = () => {};
</script>

<template>
  <AuthenticatedLayout :title="__('{entity}.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('{entity}.titles.show')"
      :breadcrumbs="[{ to: '{routePrefix}.index', text: __('{entity}.titles.entity_breadcrumb') }, { text: __('generics.actions.show') }]"
      :links="[{ to: route('{routePrefix}.index'), text: __('generics.buttons.back') }]"
    />
    <Form{Entity} :form="form" :readOnly="true" :submitHandler="submitHandler" />
  </AuthenticatedLayout>
</template>
```

**List.vue** — `Modules/{Module}/Resources/Pages/{PluralEntity}/List.vue`

```vue
<script setup>
import { ref, computed, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from 'primevue/useconfirm';
import { FilterMatchMode } from '@primevue/core/api';
import InputText from 'primevue/inputtext';

import AuthenticatedLayout from '@Core/Layouts/AuthenticatedLayout.vue';
import HeaderCrud from '@Core/Components/Crud/HeaderCrud.vue';
import Datatable from '@Core/Components/Table/Datatable.vue';
import {Entity}Service from '@{Module}/Services/{Entity}Service.js';
import { defaultDeleteHandler, debouncedFilter } from '@Core/Utils/table.js';
import { trans } from 'laravel-vue-i18n';
import { can } from '@Auth/Services/Auth';

const props = defineProps({
  toast: Object,
});

const toast = useToast();
const confirm = useConfirm();
const datatable = ref(null);

const filters = {
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
  // Add one entry per filterable column:
  // name: { value: null, matchMode: FilterMatchMode.CONTAINS },
};

const columns = computed(() => [
  // { field: 'name', header: trans('{entity}.table.name.label'), sortable: true, style: 'min-width: 200px' },
  { type: 'actions', style: 'min-width: 130px', exportable: false },
]);

const canShow = can('{routePrefix}.show');
const canEdit = can('{routePrefix}.edit');
const canDestroy = can('{routePrefix}.destroy');
const canCreate = can('{routePrefix}.create');

const headerLinks = [];
if (canCreate) {
  headerLinks.push({ to: '{routePrefix}.create', text: 'generics.new' });
}

const fetchHandler = async (params) => {
  return await {Entity}Service.list(params);
};

const deleteHandler = (record) => {
  defaultDeleteHandler(confirm, datatable, toast, () => {Entity}Service.del(record.id));
};

onMounted(async () => {
  if (props.toast) {
    toast.add(props.toast);
  }
});
</script>

<template>
  <AuthenticatedLayout :title="__('{entity}.titles.entity_breadcrumb')">
    <HeaderCrud
      :title="__('{entity}.titles.entity_breadcrumb')"
      :breadcrumbs="[{ to: '{routePrefix}.index', text: __('{entity}.titles.entity_breadcrumb') }, { text: __('generics.list') }]"
      :links="headerLinks"
    />

    <Datatable
      ref="datatable"
      :filters="filters"
      :fetchHandler="fetchHandler"
      sortField="id"
      :sortOrder="-1"
      :columns="columns"
    >
      <!-- Add filter slots per column: -->
      <!-- <template #filter-name="{ filterModel, filterCallback }">
        <InputText v-model="filterModel.value" @input="debouncedFilter(filterCallback)" fluid :placeholder="__('{entity}.table.name.placeholder')" />
      </template> -->

      <template #body-actions="{ data }">
        <Link :href="route('{routePrefix}.show', data.id)" v-if="canShow">
          <span class="material-symbols-rounded cursor-pointer transition-all text-slate-500 hover:text-sky-600">visibility</span>
        </Link>
        <Link :href="route('{routePrefix}.edit', data.id)" v-if="canEdit">
          <span class="material-symbols-rounded cursor-pointer transition-all text-slate-500 hover:text-emerald-600">edit</span>
        </Link>
        <span
          class="material-symbols-rounded cursor-pointer transition-all text-slate-500 hover:text-pink-600"
          @click="deleteHandler(data)"
          v-if="canDestroy"
        >
          delete
        </span>
      </template>
    </Datatable>
  </AuthenticatedLayout>
</template>
```

---

## Step 3: Post-Generation Checklist

After generating all files:

- [ ] Run `vendor/bin/pint --dirty --format agent` to fix PHP formatting
- [ ] Run `npm run format` to fix JS/Vue formatting
- [ ] Run `php artisan config:clear` if ServiceProvider changed
- [ ] Confirm route was added to `web.php` correctly
- [ ] Confirm singleton registered in ServiceProvider
- [ ] Ask user: "¿Quieres que agregue permisos al menú también?"

## Common Mistakes

| Mistake | Fix |
|---|---|
| Using `response()->json()` for Inertia renders | Use `Inertia::render()` |
| Forgetting `_method: 'PATCH'` in Edit.vue form | Always include in `useForm({})` |
| `form.put()` instead of `form.post()` | Always use `form.post()` with `_method: 'PATCH'` |
| Missing `toast` prop in `index()` controller | `'toast' => session('toast')` |
| Route param name mismatch in UpdateRequest | Match `$this->route('category')` to actual route param |
| Forgetting singleton in ServiceProvider | Service won't be resolved |
| JS `import` path using wrong alias | Use `@{Module}/Services/...`, not relative paths |
| `prepareForValidation` missing for PrimeVue selects | PrimeVue sends `{value, text}` objects for dropdowns |

## FK / Select Field Extras

When a column is a foreign key (e.g., `category_id`):
1. Controller `index()`, `create()`, `edit()`, `show()` must pass the select data:
   ```php
   'categories' => $this->categoryService->listAsSelect(),
   ```
2. `create()` prop in Create.vue: `defineProps({ categories: Array })`
3. In Form.vue: use `VSelect` component instead of `VInput`
4. In `prepareForValidation()`: extract `.value` from the PrimeVue select object

## Inertia Render Module Notation

`Inertia::render('Crm::Categories/List')` maps to `Modules/Crm/Resources/Pages/Categories/List.vue`

Format: `'{Module}::{PluralPascalCase}/{PageName}'`
