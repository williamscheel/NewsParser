<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\News;
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

Route::group(
    [
        'namespace'		=> 'Api',
        'as'			=> 'api',
    ],
    function()
    {
        Route::group(['prefix'=>'news', 'as'=>'.news'],
            function() {
                Route::get('/getNews', [News::class, 'getNews'])->name('.getNews');
            }
        );
    }
);
