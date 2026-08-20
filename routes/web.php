<?php

use App\Http\Controllers\QuotationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

Route::middleware(['auth'])->group(function () {
    Route::get('/projects/{project}/pdf', [QuotationController::class, 'downloadPdf'])
        ->name('projects.pdf');
});
