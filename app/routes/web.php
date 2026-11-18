<?php

use Illuminate\Support\Facades\Route;
use App\Jobs\SendWhatsappJob;

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

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => 'web'], function () {
    Route::get(env('LARAVUE_PATH'), 'LaravueController@index')->where('any', '.*')->name('laravue');
});

Route::middleware('throttle:5,1')->get('/token', function () {
    return csrf_token();
});

// Route::get('/token', function () {
//     return csrf_token(); 
// });

// Route::get('/test-wa', function () {
//     SendWhatsappJob::dispatch(['6283156916103'], 'Test WA dari Queue Laravel');
//     return 'Job dispatched';
// });