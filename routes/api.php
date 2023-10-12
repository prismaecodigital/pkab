<?php

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin', 'middleware' => ['auth:sanctum']], function () {
    // Abilities
    Route::apiResource('abilities', 'AbilitiesController', ['only' => ['index']]);

    // Locales
    Route::get('locales/languages', 'LocalesController@languages')->name('locales.languages');
    Route::get('locales/messages', 'LocalesController@messages')->name('locales.messages');

    // Permissions
    Route::resource('permissions', 'PermissionsApiController');

    // Roles
    Route::resource('roles', 'RolesApiController');

    // Users
    Route::resource('users', 'UsersApiController');

    // Bu
    Route::resource('bus', 'BuApiController');

    // Get dept based on bu
    Route::get('budept','DeptApiController@budept');
    Route::get('budept-all','DeptApiController@budeptAll');

    // Get site based on bu
    Route::get('busite', 'SiteApiController@busite');;

    // Dept
    Route::resource('depts', 'DeptApiController');

    // Pkab Item
    Route::post('pkab-items/approve', 'PkabItemApiController@approve')->name('pkab-items.approve');
    Route::post('pkab-items/reject', 'PkabItemApiController@reject')->name('pkab-items.reject');
    Route::post('pkab-items/rejectData', 'PkabItemApiController@rejectData')->name('pkab-items.rejectData');
    Route::patch('pkab-items/updateSimesra', 'PkabItemApiController@updateSimesra');
    Route::resource('pkab-items', 'PkabItemApiController');
    Route::get('pkab-items/export', 'PkabItemApiController@export')->name('pkab-items.export');

    Route::resource('pkab-dones', 'PkabDoneApiController')->parameters(['pkab-dones' => 'pkab-items']);

    // Item
    Route::resource('items', 'ItemApiController', ['except' => ['create', 'store', 'edit', 'update', 'show', 'destroy']]);

    // Status History
    Route::resource('status-histories', 'StatusHistoryApiController', ['except' => ['create', 'store', 'edit', 'update', 'show']]);

    // Site
    Route::resource('sites', 'SiteApiController');

    // Marketlist Item
    Route::resource('marketlist-items', 'MarketlistItemApiController');

    // Marketlist
    // Route::post('marketlists/approveData', 'MarketlistApiController@approveData')->name('marketlists.approveData');
    Route::post('marketlists/rejectData', 'MarketlistApiController@rejectData')->name('marketlists.rejectData');
    Route::match(['post', 'put','patch'], 'marketlists/approveData/{id}', 'MarketlistApiController@approveData')->name('marketlists.approveData');

    Route::put('marketlists/updateDataOnly/{id}', 'MarketlistApiController@updateDataOnly')->name('marketlists.updateDataOnly');
    Route::resource('marketlists', 'MarketlistApiController');

    Route::resource('marketlist-dones', 'MarketlistDoneApiController')->parameters(['marketlist-dones' => 'marketlists']);

    // Category Marketlist
    Route::resource('categories','CategoryApiController');

    // Change password
        Route::get('password/edit', 'ChangePasswordController@edit')->name('password.edit');
        Route::post('password', 'ChangePasswordController@update')->name('password.update');
        Route::post('profile', 'ChangePasswordController@updateProfile')->name('password.updateProfile');
        Route::post('profile/destroy', 'ChangePasswordController@destroy')->name('password.destroyProfile');

});

