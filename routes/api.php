<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

// AUTH
Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'AuthController@login')->name('login');
    Route::post('register', 'AuthController@register');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::get('me', 'AuthController@me');
});

// USER
Route::group(['prefix' => 'users'], function () {
    Route::get('/', 'UserController@index');
    Route::get('{user}', 'UserController@show');
    Route::put('{user}', 'UserController@update');
    Route::delete('{user}', 'UserController@destroy');
    Route::post('{user}/update_avatar', 'UserController@updateAvatar');
    Route::post('{user}/remove_avatar', 'UserController@removeAvatar');
});

// POST
Route::group(['prefix' => 'posts'], function () {
    Route::get('/', 'PostController@index');
    Route::post('/', 'PostController@store');
    Route::get('{post}', 'PostController@show');
});
