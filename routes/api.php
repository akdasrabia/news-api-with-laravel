<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\CommentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\NewsController;

Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);
 
    return ['token' => $token->plainTextToken];
});


Route::post('/register', [AuthController::class,'register']);
Route::post('/login', [AuthController::class,'login']);

Route::get('/news',[NewsController::class,'getAll']);
Route::get('/news/{slug}',[NewsController::class,'getBySlug']);

Route::get('/sendMail', [UserController::class, 'sendMail']);


//auth:sanctum

Route::group(['middleware' => ['auth:sanctum', 'custom-auth']],function(){   
    
    // User 
    Route::get('/user',[UserController::class,'userDetails']);
    Route::get('/user/news',[UserController::class,'getUserNews']);
    Route::get('/logout',[AuthController::class,'logout']);

    // News
    Route::post('/news',[NewsController::class,'create']);
    Route::put('/news/{slug}',[NewsController::class,'updateBySlug']);
    Route::delete('/news/{slug}',[NewsController::class,'deleteBySlug']);

    // Comments
    Route::get('/news/{slug}/comments',[CommentController::class,'getCommentsByNewsSlug']);
    Route::post('/news/{slug}/comments',[CommentController::class,'createComment']);
    Route::delete('/news/comments/{id}',[CommentController::class,'deleteById']);

});






