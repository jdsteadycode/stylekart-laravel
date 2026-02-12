<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Category;
use App\Models\SubCategory;

class SubcategoryController extends Controller
{
    /*
        All Sub categories
    */
    public function index()
    {
        // log the action
        Log::info(
            "[app\Http\Controllers\Admin\SubcategoryController@index] All sub categories requested!",
        );

        $subcategories = SubCategory::with("category")->latest()->get();

        // log the status
        Log::info("sub categories fetched", [
            "total" => count($subcategories),
            "status" => (bool) $subcategories,
        ]);

        return view("admin.subcategories.index", compact("subcategories"));
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

        $categories = Category::orderBy("name")->get();

        // log the status
        Log::info("Categories fetched for adding sub categories", [
            "total" => count($categories),
            "status" => (bool) $categories,
        ]);

        return view("admin.subcategories.create", compact("categories"));
    }

    /*
        when saving / storing sub category
    */

    public function store(Request $request)
    {
        // log the action
        Log::info(
            "[app\Http\Controllers\Admin\SubcategoryController@store] Sub categories storage initiated",
        );

        $request->validate(
            [
                "category_id" => "required|exists:categories,id",
                "name" => "required|string|max:255",
            ],
            [
                "category_id.required" => "Select a sub category.",
                "name.required" => "Enter a sub category name",
            ],
        );

        $created = SubCategory::create([
            "category_id" => $request->category_id,
            "name" => $request->name,
        ]);

        // log the status
        Log::info("Sub Category Created", ["status" => (bool) $created]);

        return redirect()
            ->route("admin.subcategories.index")
            ->with("success", "Sub category created successfully.");
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
        return view("admin.subcategories.edit", compact("subcategory"));
    }

    /*
        when updating / saving sub category
    */
    public function update(Request $request, SubCategory $subcategory)
    {
        Log::info(
            "[app\Http\Controllers\Admin\SubcategoryController@update] Sub Category update initiated",
        );

        $request->validate(
            [
                "name" => "required|string|max:255",
            ],
            [
                "name.required" => "Enter a sub category name.",
            ],
        );

        $updated = $subcategory->update([
            "name" => $request->name,
        ]);

        Log::info("Sub Category" . $request->name . " Updated", [
            "status" => (bool) $updated,
        ]);

        return redirect()
            ->route("admin.subcategories.index")
            ->with("success", "Sub category updated successfully.");
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
            ["status" => (bool) $deleted],
        );

        return redirect()
            ->route("admin.subcategories.index")
            ->with("success", "Subcategory deleted successfully.");
    }
}
