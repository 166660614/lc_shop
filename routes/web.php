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
Route::get('/weixin/token','Weixin\IndexController@getAccessToken');
Route::get('/weixin/valid1','Weixin\IndexController@validToken1');
Route::post('/weixin/valid1','Weixin\IndexController@wxEvent');//接受微信服务器事件推送
Route::get('/weixin/create_menus','Weixin\IndexController@zdyMenus');//创建自定义菜单
Route::get('/weixin/del_menus','Weixin\IndexController@delMenus');//删除自定义菜单
Route::get('/weixin/send_all_content','Weixin\IndexController@sendTextAll');//群发成功
Route::get('/weixin/clearapi','Weixin\IndexController@clearApi');//群发成功

//微信支付
Route::get('/weixin/pay/unified','Order\OrderController@unifiedOrder');     //微信支付下单
Route::post('/weixin/pay/notify','Order\OrderController@notify');//微信支付异步回调

Route::get('/weixin/view','Weixin\IndexController@viewRedisUsers');//redis列表
Route::post('/weixin/tag','Weixin\IndexController@addTag');
