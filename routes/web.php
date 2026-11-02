<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');
Route::redirect('/dashboard', '/admin/dashboard');

if (! app()->isProduction()) {
    Route::get('/view-php-ini', function () {
        $phpIniFile = php_ini_loaded_file();
        $phpIniContents = file_get_contents($phpIniFile);

        return response($phpIniContents)
            ->header('Content-Type', 'text/plain')
            ->header('X-Php-Ini-Location', $phpIniFile); // Add this line to include the file path
    });
}

Auth::routes(['register' => false]);

Route::group([
    'prefix'     => 'admin',
    'as'         => 'admin.',
    'middleware' => ['auth'],
], function () {
    Route::view('/{any?}', 'layouts.admin.app')->name('dashboard')->where('any', '.*');
});

Route::get('marketlist-report', [HomeController::class, 'report'])->name('marketlist.report');

if (! app()->isProduction()) {
    Route::get('/password', function () {
        return Hash::make('password');
    });
}