<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DealActivityController;
use App\Http\Controllers\DealController;
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

    // CRM & Pipeline Routes
    Route::get('/deals', [DealController::class, 'index'])->name('deals.index');
    Route::post('/deals', [DealController::class, 'store'])->name('deals.store');
    Route::put('/deals/{deal}', [DealController::class, 'update'])->name('deals.update');
    Route::patch('/deals/{deal}/stage', [DealController::class, 'updateStage'])->name('deals.stage');
    Route::delete('/deals/{deal}', [DealController::class, 'destroy'])->name('deals.destroy');

    Route::resource('clients', ClientController::class);
    Route::post('/clients/{client}/contacts', [ClientController::class, 'storeContact'])->name('clients.contacts.store');
    Route::delete('/contacts/{contact}', [ClientController::class, 'destroyContact'])->name('contacts.destroy');

    Route::get('/activities', [DealActivityController::class, 'index'])->name('activities.index');
    Route::post('/activities', [DealActivityController::class, 'store'])->name('activities.store');
    Route::delete('/activities/{activity}', [DealActivityController::class, 'destroy'])->name('activities.destroy');

    // Quotations / DevCalc Engine
    Route::get('/projects/export/csv', [ProjectController::class, 'exportCsv'])->name('projects.export.csv');
    Route::get('/projects/export/pdf', [ProjectController::class, 'exportPdf'])->name('projects.export.pdf');
    Route::post('/projects/bulk-delete', [ProjectController::class, 'bulkDestroy'])->name('projects.bulk-delete');
    Route::resource('projects', ProjectController::class);
    Route::post('/projects/{project}/addendum', [ProjectController::class, 'createAddendum'])->name('projects.addendum');
    Route::get('/projects/{project}/pdf', [QuotationController::class, 'downloadPdf'])->name('projects.pdf');

    // Modules Catalog Master Data
    Route::resource('modules', ModuleController::class)->except(['create', 'show', 'edit']);

    // Help & Documentation
    Route::get('/help', [HelpController::class, 'index'])->name('help');
});
