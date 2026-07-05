<?php

use App\Livewire\Admin\TransactionLogs;
use App\Livewire\Admin\UserManagement;
use App\Livewire\Dashboard;
use App\Livewire\Inventory\CountManagement;
use App\Livewire\Inventory\ProductCatalog;
use App\Livewire\Inventory\StockManagement;
use App\Livewire\Operations\PickingManagement;
use App\Livewire\Operations\ReceivingManagement;
use App\Livewire\Warehouse\LabelManagement;
use App\Livewire\Warehouse\LocationManagement;
use App\Livewire\Warehouse\SupplierManagement;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');
    Route::get('admin/users', UserManagement::class)->name('admin.users')->middleware('can:user.manage');
    Route::get('admin/logs', TransactionLogs::class)->name('admin.logs')->middleware('can:user.manage'); // Adjust permission if needed
    Route::get('inventory/products', ProductCatalog::class)->name('inventory.products')->middleware('can:product.manage');
    Route::get('inventory/stock', StockManagement::class)->name('inventory.stock')->middleware('can:inventory.view');
    Route::get('inventory/counts', CountManagement::class)->name('inventory.counts')->middleware('can:inventory.view');
    Route::get('warehouse/locations', LocationManagement::class)->name('warehouse.locations')->middleware('can:location.manage');
    Route::get('warehouse/labels', LabelManagement::class)->name('warehouse.labels')->middleware('can:location.manage');
    Route::get('warehouse/suppliers', SupplierManagement::class)->name('warehouse.suppliers')->middleware('can:supplier.manage');
    Route::get('operations/receiving', ReceivingManagement::class)->name('operations.receiving')->middleware('can:shipment.receive');
    Route::get('operations/picking', PickingManagement::class)->name('operations.picking')->middleware('can:order.pick');
});

require __DIR__.'/settings.php';
