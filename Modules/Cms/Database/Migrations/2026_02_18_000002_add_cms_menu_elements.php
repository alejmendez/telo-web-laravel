<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $module = DB::table('modules')->where('slug', 'cms')->first();

        $menu = [
            [
                'text' => 'cms_module.menu.cms',
                'type' => MenuTypes::main->value,
                'children' => [
                    [
                        'text' => 'cms_module.menu.posts',
                        'link' => 'post.index',
                        'icon' => '<span class="material-symbols-rounded">article</span>',
                        'active_with' => 'post.*',
                        'type' => MenuTypes::main->value,
                    ],
                ],
            ],
            [
                'text' => 'cms_module.menu.post_categories',
                'link' => 'post_category.index',
                'icon' => '<span class="material-symbols-rounded">article</span>',
                'active_with' => 'post_category.*',
                'type' => MenuTypes::right_sidebar->value,
            ],
            [
                'text' => 'cms_module.menu.tags',
                'link' => 'tag.index',
                'icon' => '<span class="material-symbols-rounded">article</span>',
                'active_with' => 'tag.*',
                'type' => MenuTypes::right_sidebar->value,
            ],
        ];

        $menuGroupOrder = 0;
        foreach ($menu as $item) {
            $menuElementOrder = 0;
            $menuGroup = DB::table('menus')->where('text', $item['text'])->first();
            if (! $menuGroup) {
                $menuGroupId = DB::table('menus')->insertGetId([
                    'text' => $item['text'],
                    'order' => $menuGroupOrder++,
                ]);
                $menuGroup = (object) ['id' => $menuGroupId];
            } else {
                DB::table('menus')->where('id', $menuGroup->id)->update([
                    'order' => $menuGroupOrder++,
                ]);
            }

            foreach ($item['children'] as $child) {
                $menuItem = DB::table('menus')->where('text', $child['text'])->first();
                if (! $menuItem) {
                    DB::table('menus')->insert([
                        'text' => $child['text'],
                        'link' => $child['link'],
                        'icon' => $child['icon'],
                        'active_with' => $child['active_with'],
                        'parent_id' => $menuGroup->id,
                        'order' => $menuElementOrder++,
                        'module_id' => $module->id,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        $module = DB::table('modules')->where('slug', 'cms')->first();
        DB::table('menus')->where('module_id', $module->id)->delete();
    }
};
