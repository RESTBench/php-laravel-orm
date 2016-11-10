<?php

use Illuminate\Http\Request;

Route::post('/login', 'Api\Auth\AuthController@authenticate');
