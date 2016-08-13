<?php

/**
 * Get dingo router
 */
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {

    //, 'middleware' => '\Barryvdh\Cors\HandleCors::class'
    $api->group(['namespace' => 'Api\Controllers'], function ($api) {

        //public endpoints
        $api->get('users/rules', 'UserController@rules');
        $api->post('users', 'UserController@store');
        $api->post('auth', 'UserController@authenticate');

        $api->group( [ 'middleware' => 'jwt.auth' ], function ($api) {
            
            $api->get('validate-token', 'UserController@validateToken');
            $api->get('users', 'UserController@index');
            $api->get('users/me', 'UserController@me');
            $api->post('users/upload', 'UserController@upload');
            
            $api->get('users/{id}', 'UserController@show');
            $api->post('users/{id}', 'UserController@update');
            $api->delete('users/{id}', 'UserController@destroy');
            
            
        });
    });
});

