<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Category;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function index()
    {
        // Log the action
        Log::info(
            "[app\Http\Controllers\Admin\CategoryController@index] All Categories fetching!",
        );

        $categories = Category::latest()->get();

        // log the status
        Log::info("Categories fetched successfully", [
            "total" => count($categories),
        ]);

        return view("admin.categories.index", compact("categories"));
    }

    /*
        when showing create category form
    */
    public function create()
    {
        Log::info(
            "[app\Http\Controllers\Admin\CategoryController@create] Create Category Page Showed",
        );

        return view("admin.categories.create");
    }

    /*
        when saving / creating category
    */
    public function store(Request $request)
    {
        $request->validate(
            [
                "name" => "required|string|max:255|unique:categories,name",
            ],
            [
                "name.required" => "Category name is needed!",
                "name.string" => "Category must be strictly text",
                "name.max" => "Category must be within 255 characters",
            ],
        );

        $status = Category::create([
            "name" => $request->name,
        ]);

        // log status
        Log::info("Category created successfully", [
            "name" => $request->name,
            "status" => (bool) $status,
        ]);

        return redirect()
            ->route("admin.categories.index")
            ->with("success", "Category added successfully!");
    }

    /*
        when viewing edit category form
    */
    public function edit(Category $category)
    {
        Log::info(
            "[app\Http\Controllers\Admin\CategoryController@edit] Edit Category Page Shown",
            [
                "id" => $category->id,
            ],
        );

        return view("admin.categories.edit", compact("category"));
    }

    public function update(Request $request, Category $category)
    {
        // log the action
        Log::info(
            "[app\Http\Controllers\Admin\CategoryController@destroy] Category updation initiated!",
        );

        $request->validate(
            [
                "name" =>
                    "required|string|max:255|unique:categories,name," .
                    $category->id,
            ],
            [
                "name.required" => "Category name is needed!",
                "name.string" => "Category must be strictly text",
                "name.max" => "Category must be within 255 characters",
            ],
        );

        $status = $category->update([
            "name" => $request->name,
        ]);

        // Log the status
        Log::info("Category updated successfully", [
            "name" => $request->name,
            "status" => (bool) $status,
        ]);

        return redirect()
            ->route("admin.categories.index")
            ->with("success", "Category updated successfully!");
    }

    /*
        Remove existing category
    */
    public function destroy(Category $category)
    {
        // log the action
        Log::info(
            "[app\Http\Controllers\Admin\CategoryController@destroy] Category deletion initiated!",
        );

        $status = $category->delete();

        // log the status
        Log::warning("Category deleted!", [
            "name" => $category->name,
            "status" => (bool) $status,
        ]);

        return redirect()
            ->route("admin.categories.index")
            ->with("success", "Category deleted successfully!");
    }
}
