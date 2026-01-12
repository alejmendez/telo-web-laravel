<?php

namespace Modules\Crm\Services;

use Modules\Crm\Models\Category;
use Modules\Core\Services\PrimevueDatatables;

class CategoryService
{
    protected const SEARCHABLE_COLUMNS = ['name', 'slug'];

    public function list(Array $params = [])
    {
        $query = Category::query();

        $datatable = new PrimevueDatatables($params, self::SEARCHABLE_COLUMNS);
        $categories = $datatable->of($query)->make();

        return $categories;
    }

    public function find(int $id): Category
    {
        return Category::findOrFail($id);
    }

    public function create(Array $data): Category
    {
        $category = new Category;
        $category->name = $data['name'];
        $category->slug = $data['slug'] ?? Str::slug($data['name']);
        $category->save();

        return $category;
    }

    public function update(int $id, Array $data): Category
    {
        $category = $this->find($id);
        $category->name = $data['name'] ?? $category->name;
        $category->slug = $data['slug'] ?? $category->slug;
        $category->save();

        return $category;
    }

    public function delete(int $id): bool
    {
        $category = $this->find($id);
        return $category->delete();
    }
}
