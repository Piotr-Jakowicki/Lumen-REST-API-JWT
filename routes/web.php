<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api'], function () use ($router) {

    // Auth
    $router->post('/register', 'Api\AuthController@register');
    $router->post('/login', 'Api\AuthController@login');
    $router->get('/me', 'Api\AuthController@me');
    $router->post('/logout', 'Api\AuthController@logout');
    $router->get('/refresh', 'Api\AuthController@refresh');

    // Categories
    $router->get('/categories', 'Api\CategoryController@index');
    $router->get('/categories/{id}', 'Api\CategoryController@show');
    $router->delete('/categories/{id}', 'Api\CategoryController@destroy');
    $router->post('/categories', 'Api\CategoryController@store');
    $router->patch('/categories/{id}', 'Api\CategoryController@update');

    //Images
    $router->get('/images', 'Api\ImageController@index');
    $router->get('/images/{id}', 'Api\ImageController@show');
    $router->delete('/images/{id}', 'Api\ImageController@destroy');
    $router->post('/images', 'Api\ImageController@store');
    $router->patch('/images/{id}', 'Api\ImageController@update');

    //Collections
    $router->get('/collections', 'Api\CollectionController@index');
    $router->get('/collections/{id}', 'Api\CollectionController@show');
    $router->delete('/collections/{id}', 'Api\CollectionController@destroy');
    $router->post('/collections', 'Api\CollectionController@store');
    $router->patch('/collections/{id}', 'Api\CollectionController@update');
});
