<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
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
        Log::info('Categories fetched successfully', [
            'total' => count($categories),
        ]);

        return view('admin.categories.index', compact('categories'));
    }

    /*
        when showing create category form
    */
    public function create()
    {
        Log::info(
            "[app\Http\Controllers\Admin\CategoryController@create] Create Category Page Showed",
        );

        return view('admin.categories.create');
    }

    /*
        when saving / creating category
    */
    public function store(CategoryRequest $request)
    {

        $status = Category::create([
            'name' => $request->name,
        ]);

        // log status
        Log::info('Category created successfully', [
            'name' => $request->name,
            'status' => (bool) $status,
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category added successfully!');
    }

    /*
        when viewing edit category form
    */
    public function edit(Category $category)
    {
        Log::info(
            "[app\Http\Controllers\Admin\CategoryController@edit] Edit Category Page Shown",
            [
                'id' => $category->id,
            ],
        );

        return view('admin.categories.edit', compact('category'));
    }

    public function update(CategoryRequest $request, Category $category)
    {
        // log the action
        Log::info(
            "[app\Http\Controllers\Admin\CategoryController@destroy] Category updation initiated!",
        );

        $status = $category->update([
            'name' => $request->name,
        ]);

        // Log the status
        Log::info('Category updated successfully', [
            'name' => $request->name,
            'status' => (bool) $status,
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category updated successfully!');
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
        Log::warning('Category deleted!', [
            'name' => $category->name,
            'status' => (bool) $status,
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category deleted successfully!');
    }
}
