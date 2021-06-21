<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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

Auth::routes();

Route::get('/','ArticleController@index')->name('articles.index');
Route::resource('/articles', 'ArticleController')->except(['index', 'show'])->middleware('auth');
Route::resource('/articles', 'ArticleController')->only('show');

Route::prefix('articles')->name('articles.')->group(function(){
    Route::put('/{article}/like', 'ArticleController@like')->name('like')->middleware('auth');
    Route::delete('/{article}/like', 'ArticleController@unlike')->name('unlike')->middleware('auth');

});

Route::get('/tags/{name}', 'TagController@show')->name('tags.show');
// prefixメソッド, nameメソッドによって下記の文が上記の様な記述に簡略化できる
// Route::put('articles/{article}/like', 'ArticleController@like')->name('articles.like')->middleware('auth');
// Route::delete('articles/{article}/like', 'ArticleController@unlike')->name('articles.unlike')->middleware('auth');

Route::prefix('users')->name('users.')->group(function(){
    Route::get('/{name}', 'UseController@show')->name('show');
});
