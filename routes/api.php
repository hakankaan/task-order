<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([], function () {
    Route::get('/tasks', 'Task\TaskController@all');
    Route::get('/task-order', 'Task\TaskController@taskOrder');
    Route::post('/create-task', 'Task\TaskController@createTask');
    Route::put('/add-prerequisities', 'Task\TaskController@addPrerequisities');
});
