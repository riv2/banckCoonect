<?php

use App\Services\Translit;
use DevDojo\Chatter\Helpers\ChatterHelper;
use Illuminate\Database\Seeder;
use App\Chatter\Models\Category;

class EntToChatterCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $foldersList = array_merge(config('ent.folders.kz'), config('ent.folders.ru'));

        foreach ($foldersList as $k => $folder) {
            $category = Category::where('slug', $k . '-ent')->first();

            if (empty($category)) {
                $category = new Category();
                $category->order = 1;
                $category->name = $folder;
                $category->color = '#'.ChatterHelper::stringToColorCode(Translit::get($k));
                $category->slug = $k.'-ent';
                $category->save();
            }
        }
    }
}
