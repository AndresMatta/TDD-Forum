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
    return view('welcome');
});

Auth::routes(['verify' => true]);
// Home
Route::get('/home', 'HomeController@index')->middleware('verified')->name('home');
// Users
Route::get('/profiles/{user}', 'ProfileController@show')->name('profile');
Route::resource('/api/users', 'Api\UserController');
Route::post('/api/users/{user}/avatar', 'Api\UserAvatarController@store')->name('avatar');
// Notifications
Route::get('/profiles/{user}/notifications', 'UserNotificationController@index');
Route::delete('/profiles/{user}/notifications/{notification}', 'UserNotificationController@destroy');
// Threads
Route::get('/threads', 'ThreadController@index')->name('threads');
Route::get('/threads/create', 'ThreadController@create');
Route::get('/threads/{channel}/{thread}', 'ThreadController@show');
Route::patch('/threads/{channel}/{thread}', 'ThreadController@update')->name('threads.update');
Route::delete('/threads/{channel}/{thread}', 'ThreadController@destroy');
Route::post('/threads', 'ThreadController@store');
Route::get('/threads/{channel}', 'ThreadController@index');
// Replies
Route::get('/threads/{channel}/{thread}/replies', 'ReplyController@index');
Route::post('/threads/{channel}/{thread}/replies', 'ReplyController@store');
Route::patch('/replies/{reply}', 'ReplyController@update');
Route::delete('/replies/{reply}', 'ReplyController@destroy')->name('replies.destroy');
// Favorites
Route::post('/replies/{reply}/favorites', 'FavoriteController@store');
Route::delete('/replies/{reply}/favorites', 'FavoriteController@destroy');
// Subscriptions
Route::post('threads/{channel}/{thread}/subscriptions', 'ThreadSubscriptionController@store');
Route::delete('threads/{channel}/{thread}/subscriptions', 'ThreadSubscriptionController@destroy');
// Best replies
Route::post('/replies/{reply}/best', 'BestReplyController@store')->name('best-replies.store');
// Lock Threads
Route::patch('locked-threads/{thread}', 'LockedThreadController@update')->name('locked-threads.update')->middleware('admin');
