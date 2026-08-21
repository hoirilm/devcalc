<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\QuotationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/projects/export/csv', [ProjectController::class, 'exportCsv'])->name('projects.export.csv');
    Route::get('/projects/export/pdf', [ProjectController::class, 'exportPdf'])->name('projects.export.pdf');
    Route::post('/projects/bulk-delete', [ProjectController::class, 'bulkDestroy'])->name('projects.bulk-delete');
    Route::resource('projects', ProjectController::class);
    Route::post('/projects/{project}/addendum', [ProjectController::class, 'createAddendum'])->name('projects.addendum');
    Route::get('/projects/{project}/pdf', [QuotationController::class, 'downloadPdf'])->name('projects.pdf');

    Route::resource('modules', ModuleController::class)->except(['create', 'show', 'edit']);

    Route::get('/help', [HelpController::class, 'index'])->name('help');
});
