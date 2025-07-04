<?php

use App\Http\Controllers\CompanyController;
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

// query time:2.32 , 2mb 
// Route::get('/users', [UsersController::class, 'searchwithIndividualQuery']);

// query time : 2.3
Route::get('/users', [UsersController::class, 'searchWithUnion']);

/*
Route::get('/users/company={company}', [UsersController::class, 'filterByCompany'])
    ->name('users.byCompany');

Route::get('/users/subquery', [UsersController::class, 'subQuery'])
    ->name('users.byCompany');

Route::get('/companies', [CompanyController::class, 'index'])->name('companies.');
*/