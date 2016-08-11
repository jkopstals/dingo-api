<?php

/**
 * Get dingo router
 */
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {

    //, 'middleware' => '\Barryvdh\Cors\HandleCors::class'
    $api->group(['namespace' => 'Api\Controllers'], function ($api) {

        //public endpoints
        $api->post('auth', 'UserController@authenticate');
        $api->get('users/rules', 'UserController@rules');
        $api->post('users', 'UserController@store');

        $api->group( [ 'middleware' => 'jwt.auth' ], function ($api) {
            $api->get('validate_token', 'UserController@validateToken');

            $api->get('users/me', 'UserController@me');
            $api->get('users', 'UserController@index');
            $api->get('users/{id}', 'UserController@show');
        });
    });
});

