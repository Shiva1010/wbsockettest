<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    event(new \App\Events\MessageSent( App\Sheep::find(1), App\Message::find(1)));
    return view('welcome');
});


Route::get('/google/auth', 'SocialiteController@RedirectToProvider');
Route::post('/google/auth/callback', 'SocialiteController@HandleProviderCallback');