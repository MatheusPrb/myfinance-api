<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        if (! Category::query()->where('name', 'uber')->exists()) {
            $uber = new Category;
            $uber->id = '1bfd4e71-8ff1-475e-9f0e-483a902979f5';
            $uber->name = 'uber';
            $uber->save();

            $uberSub = new Subcategory;
            $uberSub->id = '3fafedf0-c692-4083-aa06-63d3d612dff4';
            $uberSub->category_id = $uber->id;
            $uberSub->name = 'uberParaTrabalho';
            $uberSub->save();
        }

        if (! Category::query()->where('name', 'Alimentação')->exists()) {
            $alimentacao = new Category;
            $alimentacao->id = '4100ddea-3db1-49a1-a453-d3f8fe6df900';
            $alimentacao->name = 'Alimentação';
            $alimentacao->save();

            $flash = new Subcategory;
            $flash->id = (string) Str::uuid();
            $flash->category_id = $alimentacao->id;
            $flash->name = 'flash';
            $flash->save();
        }
    }
}
