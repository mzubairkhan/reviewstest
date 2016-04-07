<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

//Route::get('/', 'UserController@login');

//Route::get('authenticate', 'UserController@authenticate');


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    Route::get('/', 'UserController@login');

    Route::get('user/logout', 'UserController@logout');

    Route::get('authenticate', 'UserController@authenticate');

    Route::get('admin/index', 'AdminController@index');

    Route::get('admin/users', 'AdminController@users');

    Route::get('admin/editUser/{id}', 'AdminController@editUser');

    Route::get('admin/newUser', 'AdminController@newUser');

    Route::get('admin/pages', 'AdminController@pages');

    Route::get('admin/newPage', 'AdminController@newPage');

    Route::get('admin/editPage/{id}', 'AdminController@editPage');

    Route::get('admin/providers', 'AdminController@providers');

    Route::get('admin/editProvider/{id}', 'AdminController@editProvider');

    Route::get('admin/newProvider', 'AdminController@newProvider');

    Route::get('admin/customfields', 'AdminController@customfields');

    Route::get('admin/editCustomfield/{id}', 'AdminController@editCustomfield');

    Route::get('admin/newCustomfield', 'AdminController@newCustomfield');

    Route::get('admin/websites', 'AdminController@websites');

    Route::get('admin/newWebsite', 'AdminController@newWebsite');

    Route::get('admin/editWebsite/{id}', 'AdminController@editWebsite');

    Route::get('admin/git', 'AdminController@git');

    Route::get('admin/newMedia', 'AdminController@newMedia');

    Route::get('admin/media', 'AdminController@media');

    Route::get('admin/roles', 'AdminController@roles');

    Route::get('admin/permissions', 'AdminController@permissions');

    Route::get('admin/modules', 'AdminController@modules');

    Route::get('admin/newModule', 'AdminController@newModule');

    Route::get('admin/newRole', 'AdminController@newRole');

    Route::get('admin/newPermission', 'AdminController@newPermission');

    Route::get('admin/editPermission/{id}', 'AdminController@editPermission');

    Route::get('admin/userRoles/{id}', 'AdminController@userRoles');

    Route::get('admin/rolePermissions/{id}', 'AdminController@rolePermissions');

    Route::get('admin/modulePermissions/{id}', 'AdminController@modulePermissions');

    Route::get('admin/permissionRoles/{id}', 'AdminController@permissionRoles');

    Route::get('registration','RegistrationController@index');

    Route::get('registration/create','RegistrationController@create');
});



