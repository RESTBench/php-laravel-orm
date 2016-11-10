<?php

use Illuminate\Http\Request;

Route::post('/login', 'Api\Auth\AuthController@authenticate');
Route::resource('/contacts', 'Api\ContactController',  [
        'except' => ['create', 'edit']
]);
