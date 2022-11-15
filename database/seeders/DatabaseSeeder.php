<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Http\Services\CategoryService;
use App\Http\Services\ProductService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $categoryService = new CategoryService();
        $clothesCategory = $categoryService->store(['name' => 'clothes']);
        $foodCategory = $categoryService->store(['name' => 'food']);
        $plantsCategory = $categoryService->store(['name' => 'plants', 'parent_id' => $foodCategory->id]);
        $fruitCategory = $categoryService->store(['name' => 'fruits', 'parent_id' => $plantsCategory->id]);

        $productService = new ProductService();
        $productService->store(['name' => 'banana', 'category_id' => $fruitCategory->id]);
        $productService->store(['name' => 'potato', 'category_id' => $plantsCategory->id]);
        $productService->store(['name' => 'lasagna', 'category_id' => $foodCategory->id]);
        $productService->store(['name' => 'dress', 'category_id' => $clothesCategory->id]);
        $productService->store(['name' => 'gloves', 'category_id' => $clothesCategory->id, 'description' => 'banana gloves']);
    }
}
