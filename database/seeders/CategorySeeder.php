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
            $uber->id = (string) Str::uuid();
            $uber->name = 'uber';
            $uber->save();

            $uberSub = new Subcategory;
            $uberSub->id = (string) Str::uuid();
            $uberSub->category_id = $uber->id;
            $uberSub->name = 'uberParaTrabalho';
            $uberSub->save();
        }

        if (! Category::query()->where('name', 'Alimentação')->exists()) {
            $alimentacao = new Category;
            $alimentacao->id = (string) Str::uuid();
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
