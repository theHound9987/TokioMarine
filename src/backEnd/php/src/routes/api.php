<?php

use App\Http\Controllers\PostTagController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GetNotesController;
use App\Http\Controllers\GetTagsController;
use App\Http\Controllers\PostNoteController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/notes', GetNotesController::class);
Route::post('/notes', PostNoteController::class);
Route::get('/tags', GetTagsController::class);
Route::post('/tags', PostTagController::class);
