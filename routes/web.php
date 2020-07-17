<?php

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

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->group(['prefix' => 'conta'], function () use ($router) {
        $router->post('criar', 'ContaController@create');
        $router->get('saldo', 'ContaController@accountsBalance');
        $router->get('saldo/{cpf}', 'ContaController@balance');
        $router->post('debito', 'ContaController@debit');
        $router->get('extrato', 'ContaController@extrato');
        $router->post('credito', 'ContaController@credit');
        $router->get('tranferencia', 'ContaController@tranferencia');
    });
});