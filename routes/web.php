<?php

use App\Livewire\Admin\UserManagement;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('admin/users', UserManagement::class)->name('admin.users')->middleware('can:user.manage');
});

require __DIR__.'/settings.php';
