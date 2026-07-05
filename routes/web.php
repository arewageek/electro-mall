<?php

use App\Livewire\Admin\UserManagement;
use App\Livewire\Inventory\ProductCatalog;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('admin/users', UserManagement::class)->name('admin.users')->middleware('can:user.manage');
    Route::get('inventory/products', ProductCatalog::class)->name('inventory.products')->middleware('can:product.manage');
});

require __DIR__.'/settings.php';
