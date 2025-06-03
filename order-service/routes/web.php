<?php

use App\Http\Controllers\OrderController;
// use Illuminate\Support\Facades\Route;

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->group(['prefix' => 'orders'], function () use ($router) {
    $router->get('/', 'OrderController@index');
    $router->post('/', 'OrderController@store');
    $router->get('/{uuid}', 'OrderController@show');
    $router->patch('/{uuid}/payment-status', 'OrderController@updatePaymentStatus');
});

