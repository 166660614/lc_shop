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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

//测试文件上传
Route::get('/upload','Upload\TestUpload@upload');
Route::post('/upload/pdf','Upload\TestUpload@uploadpdf');
//电影
Route::get('/move/seat','Move\IndexController@index');
Route::post('/move/order','Move\IndexController@order');
//微信公众号
Route::get('/weixin/valid','Weixin\IndexController@wxToken');
Route::post('/weixin/valid','Weixin\IndexController@wxEvent');