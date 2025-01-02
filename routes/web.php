<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');
Route::get('/view-php-ini', function () {
    $phpIniFile = php_ini_loaded_file();
    $phpIniContents = file_get_contents($phpIniFile);

    return response($phpIniContents)
        ->header('Content-Type', 'text/plain')
        ->header('X-Php-Ini-Location', $phpIniFile); // Add this line to include the file path
});
Route::redirect('/dashboard', '/admin/dashboard');

Auth::routes(['register' => false]);

Route::group([
    'prefix'     => 'admin',
    'as'         => 'admin.',
    'namespace'  => 'Admin',
    'middleware' => ['auth'],
], function () {
    Route::view('/{any?}', 'layouts.admin.app')->name('dashboard')->where('any', '.*');
});

Route::get('marketlist-report', 'HomeController@report')->name('marketlist.report');

Route::get('/password', function() {
    return Hash::make('password');
});