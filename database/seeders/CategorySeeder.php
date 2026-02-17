<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Category;

use Illuminate\Support\Facades\Log;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Log the action
        Log::info(
            "[CategorySeeder@run] Seeding for Category & Sub Category is initiated",
        );

        // add one category
        $women = Category::create(["name" => "Women"]);

        // log the category addon
        Log::info("Trying to add " . $women->name, ["status" => (bool) $women]);

        // add sub category
        // 1.
        $women->subcategories()->updateOrCreate(
            [
                "name" => "Footwear",
            ],
            ["name" => "Footwear"],
        );

        // log the addon
        Log::info("Added Footwear for " . $women->name);

        // 2.
        $women->subcategories()->updateOrCreate(
            [
                "name" => "Topwear",
            ],
            ["name" => "Topwear"],
        );

        // log the addon
        Log::info("Added Topwear for " . $women->name);
    }
}
