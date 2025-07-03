<?php

use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/users', [UsersController::class, 'eagerLoad']);

Route::get('/users/company={company}', [UsersController::class, 'filterByCompany'])
    ->name('users.byCompany');

Route::get('/users/subquery', [UsersController::class, 'subQuery'])
    ->name('users.byCompany');
