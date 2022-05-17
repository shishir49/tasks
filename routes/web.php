<?php

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

Route::get('/','App\Http\Controllers\Main@index');
Route::post('/get-customer','App\Http\Controllers\Main@getCustomer');
Route::post('/store-csv-data','App\Http\Controllers\Main@storeCsvData');
Route::post('/store-pdf','App\Http\Controllers\Main@storePdf');