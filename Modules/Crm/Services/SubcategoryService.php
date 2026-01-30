<?php

namespace Modules\Crm\Services;

use Modules\Crm\Models\Subcategory;
use Modules\Core\Services\PrimevueDatatables;
use Illuminate\Support\Str;

class SubcategoryService
{
    protected const SEARCHABLE_COLUMNS = ['name', 'slug'];

    public function list(Array $params = [])
    {
        $query = Subcategory::with('category');

        $datatable = new PrimevueDatatables($params, self::SEARCHABLE_COLUMNS);
        $subcategories = $datatable->of($query)->make();

        return $subcategories;
    }

    public function listAsSelect(array $filter = [])
    {
        return query_to_select(Subcategory::select('id as value', 'name as text')->orderBy('name'), ['id', 'name', 'slug'], $filter);
    }

    public function find(int $id): Subcategory
    {
        return Subcategory::findOrFail($id);
    }

    public function create(Array $data): Subcategory
    {
        $subcategory = new Subcategory;
        $subcategory->category_id = $data['category_id'];
        $subcategory->name = $data['name'];
        $subcategory->slug = $data['slug'] ?? Str::slug($data['name']);
        $subcategory->save();

        return $subcategory;
    }

    public function update(int $id, Array $data): Subcategory
    {
        $subcategory = $this->find($id);
        $subcategory->category_id = $data['category_id'] ?? $subcategory->category_id;
        $subcategory->name = $data['name'] ?? $subcategory->name;
        $subcategory->slug = $data['slug'] ?? $subcategory->slug;
        $subcategory->save();

        return $subcategory;
    }

    public function delete(int $id): void
    {
        $subcategory = $this->find($id);
        $subcategory->delete();
    }
}
