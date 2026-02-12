<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubcategoryController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin dashboard
Route::middleware(['auth', 'role:admin'])->prefix('dashboard/admin')->group(function () {

    // 'dashboard/admin/'
    Route::get('/', function () {
        return view('admin.dashboard.index');
    })->name('dashboard.admin');

    /*
        categories
        module
    */
    // 'dashboard/admin/categories'
    Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');

    // 'dashboard/admin/categories/create'
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('admin.categories.create');

    // 'dashboard/admin/categories/store'
    Route::post('/categories/store', [CategoryController::class, 'store'])->name('admin.categories.store');

    // 'dashboard/admin/categories/3/edit'
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('admin.categories.edit');

    // 'dashboard/admin/categories/3'
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('admin.categories.update');

    // 'dashboard/admin/categories/3'
    Route::delete('/categories/{category}/delete', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');


    /*
        subcategories
        module
    */
    // 'dashboard/admin/subcategories'
    Route::get('/subcategories', [SubcategoryController::class, 'index'])->name('admin.subcategories.index');

    // 'dashboard/admin/subcategories/create'
    Route::get('/subcategories/create', [SubcategoryController::class, 'create'])->name('admin.subcategories.create');

    // 'dashboard/admin/subcategories/store'
    Route::post('/subcategories', [SubcategoryController::class, 'store'])
        ->name('admin.subcategories.store');


    // 'dashboard/admin/subcategories/4/edit'
    Route::get('/subcategories/{subcategory}/edit', [SubcategoryController::class, 'edit'])
        ->name('admin.subcategories.edit');

    // 'dashboard/admin/subcategories/4'
    Route::put('/subcategories/{subcategory}/', [SubcategoryController::class, 'update'])
        ->name('admin.subcategories.update');

    // 'dashboard/admin/subcategories/4/delete'
    Route::delete('/subcategories/{subcategory}/delete', [SubcategoryController::class, 'destroy'])
        ->name('admin.subcategories.destroy');
});

// Vendor dashboard
Route::get('/dashboard/vendor', function () {
    return view('dashboard.vendor');
})->middleware(['auth', 'role:vendor'])->name('dashboard.vendor');

// Customer dashboard
Route::get('/dashboard/customer', function () {
    return view('dashboard.customer');
})->middleware(['auth', 'role:customer'])->name('dashboard.customer');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
