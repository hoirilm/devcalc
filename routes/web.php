<?php

use App\Http\Controllers\QuotationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

Route::get('/language/{locale}', function (Request $request, string $locale) {
    if (in_array($locale, ['id', 'en'])) {
        session(['locale' => $locale]);
    }

    return redirect()->back();
})->name('language.switch');

Route::middleware(['auth'])->group(function () {
    Route::get('/projects/{project}/pdf', [QuotationController::class, 'downloadPdf'])
        ->name('projects.pdf');
});
