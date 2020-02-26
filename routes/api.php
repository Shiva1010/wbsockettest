<?php

use Illuminate\Http\Request;
use App\Message;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register','SheepController@store');
Route::post('/login','SheepController@login');

Route::group(['middleware' => ['auth:sheep']], function(){
    Route::get('/test/{name}',function ($name){
        return $name;
    });

    Route::post('/chat',function (){
        $message = Message::forceCreate(request(['body']));
        event(
            (new \App\Events\MessageSent($message)));
    });
    Route::get('/messages', 'ChatsController@fetchMessages');
    Route::post('/messages', 'ChatsController@sendMessage');
});


Route::get('/google/auth', 'SocialiteController@RedirectToProvider');
Route::get('/google/auth/callback', 'SocialiteController@HandleProviderCallback');

Route::post('/google/auth/checktoken', 'SocialiteController@CheckToken');
Route::post('/google/auth/checkandroidtoken', 'SocialiteController@CheckAndroidToken');

Route::get('/privacypolicy','SheepController@privacypolicy');

//Route::post('/messages', 'ChatsController@sendMessage');