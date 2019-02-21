<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->resource('/weixin/userinfo', WxController::class);
    $router->resource('/weixin/media', WxMediaController::class);
    $router->resource('/weixin/send', WxSendController::class);
    $router->resource('/weixin/addnews', PmMediaController::class);
    $router->post('/weixin/send', 'WxSendController@send');
    $router->post('/weixin/addnews', 'PmMediaController@addnews');
});