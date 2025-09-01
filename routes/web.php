<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/data', [NewsController::class,'getResults']);
Route::get('/chart-data', [NewsController::class, 'getChartData'])->name('chart.data');
Route::post('/api/search-news', [NewsController::class, 'search']);
Route::post('/api/render-search-results', function (Request $request) {
    $articles = $request->input('articles', []);
    return view('components.search-results', compact('articles'))->render();
});

