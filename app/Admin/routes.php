<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');

    $router->resource('categories', 'CategoriesController')->except(['show'])->names('admin.categories');
    $router->get('api/categories/{is_directory?}', 'CategoriesController@apiIndex')->name('admin.api.categories');

    $router->resource('foods', 'FoodsController')->except(['show'])->names('admin.foods');
    $router->get('api/foods', 'FoodsController@apiIndex')->name('admin.api.foods');

    $router->resource('cookbooks', 'CookBooksController')->except(['show'])->names('admin.cookbooks');

});
