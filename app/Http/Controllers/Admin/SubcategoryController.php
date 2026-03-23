<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubcategoryRequest;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubcategoryController extends Controller
{
    /*
        All Sub categories
    */
    public function index(Request $request)
    {
        // log the action
        Log::info(
            "[app\Http\Controllers\Admin\SubcategoryController@index] All sub categories requested!",
        );

        // get main categories
        $categories = Category::all();

        // check if filter category?
        $categoryId = $request->query('category');
        if ($categoryId) {
            // get all sub categories of incoming category.
            $subCategories = SubCategory::where('category_id', $categoryId)
                ->with('category')
                ->get();

            // log the status
            Log::info("sub categories fetched of $categoryId Category.", [
                'total' => count($subCategories),
                'status' => (bool) $subCategories,
            ]);
        }

        // All sub categories with main category
        else {
            $subCategories = SubCategory::with('category')->latest()->get();

            // log the status
            Log::info('sub categories fetched', [
                'total' => count($subCategories),
                'status' => (bool) $subCategories,
            ]);
        }

        // log the end
        Log::info('Subcategories fetch complete!');

        return view(
            'admin.subcategories.index',
            compact('categories', 'subCategories'),
        );
    }

    /*
        when showing new sub category form
    */
    public function create()
    {
        // log the action
        Log::info(
            "[app\Http\Controllers\Admin\SubcategoryController@create] Sub categories creation initiated",
        );

        $categories = Category::orderBy('name')->get();

        // log the status
        Log::info('Categories fetched for adding sub categories', [
            'total' => count($categories),
            'status' => (bool) $categories,
        ]);

        return view('admin.subcategories.create', compact('categories'));
    }

    /*
        when saving / storing sub category
    */

    public function store(SubcategoryRequest $request)
    {
        // log the action
        Log::info(
            "[app\Http\Controllers\Admin\SubcategoryController@store] Sub categories storage initiated",
        );

        $created = SubCategory::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
        ]);

        // log the status
        Log::info('Sub Category Created', ['status' => (bool) $created]);

        return redirect()
            ->route('admin.subcategories.index')
            ->with('success', 'Sub category created successfully.');
    }

    /*
        When showing sub category edit form
    */
    public function edit(SubCategory $subcategory)
    {
        Log::info(
            "[app\Http\Controllers\Admin\SubcategoryController@edit] Sub Category edit initiated",
        );

        // No need to fetch all categories if you are not changing parent
        return view('admin.subcategories.edit', compact('subcategory'));
    }

    /*
        when updating / saving sub category
    */
    public function update(SubcategoryRequest $request, SubCategory $subcategory)
    {
        Log::info(
            "[app\Http\Controllers\Admin\SubcategoryController@update] Sub Category update initiated",
        );

        $updated = $subcategory->update([
            'name' => $request->name,
        ]);

        Log::info('Sub Category'.$request->name.' Updated', [
            'status' => (bool) $updated,
        ]);

        return redirect()
            ->route('admin.subcategories.index')
            ->with('success', 'Sub category updated successfully.');
    }

    /**
     * Delete a subcategory
     */
    public function destroy(SubCategory $subcategory)
    {
        // Log the action
        Log::info(
            "[app\Http\Controllers\Admin\SubcategoryController@destroy] Subcategory deletion initiated!",
        );

        $deleted = $subcategory->delete();

        // Log success
        Log::info(
            "[app\Http\Controllers\Admin\SubcategoryController@destroy] Subcategory deleted successfully",
            ['status' => (bool) $deleted],
        );

        return redirect()
            ->route('admin.subcategories.index')
            ->with('success', 'Subcategory deleted successfully.');
    }
}
