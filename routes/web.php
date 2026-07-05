<?php

use App\Livewire\Admin\UserManagement;
use App\Livewire\Inventory\ProductCatalog;
use App\Livewire\Warehouse\SupplierManagement;
use App\Livewire\Warehouse\LocationManagement;
use App\Livewire\Inventory\StockManagement;
use App\Livewire\Inventory\CountManagement;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('admin/users', UserManagement::class)->name('admin.users')->middleware('can:user.manage');
    Route::get('inventory/products', ProductCatalog::class)->name('inventory.products')->middleware('can:product.manage');
    Route::get('inventory/stock', StockManagement::class)->name('inventory.stock')->middleware('can:inventory.view');
    Route::get('inventory/counts', CountManagement::class)->name('inventory.counts')->middleware('can:inventory.view');
    Route::get('warehouse/locations', LocationManagement::class)->name('warehouse.locations')->middleware('can:location.manage');
    Route::get('warehouse/suppliers', SupplierManagement::class)->name('warehouse.suppliers')->middleware('can:supplier.manage');
});

require __DIR__.'/settings.php';
