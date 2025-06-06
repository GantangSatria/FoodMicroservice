<?php

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

$router->post('/payments', 'PaymentController@create');
$router->post('/payments/callback', 'PaymentController@handleCallback');
$router->post('/payments/webhook', 'PaymentController@handleWebhook');

$router->get('/test-payment', function () {
    return response()->file(resource_path('views/test.html'));
});