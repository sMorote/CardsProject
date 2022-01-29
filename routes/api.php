<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CardsController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\UserCardController;

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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/recoverpass', [AuthController::class, 'recoverPass']);
Route::get('/buy',[UserCardController::class,'buy']);

Route::middleware(['auth:sanctum','admin'])->group(function(){
    Route::post('/cardregist', [CardsController::class, 'registerCard']);
    Route::post('/collectionregist', [CollectionController::class, 'createCollection']);
    Route::post('/addcardstocollection', [CardsController::class, 'addCardToCollection']);
});

Route::middleware(['auth:sanctum','noadmin'])->group(function(){
    Route::post('/cardsale', [CardsController::class, 'saleCards']);
    Route::get('/search', [CardsController::class, 'searchCard']);
});