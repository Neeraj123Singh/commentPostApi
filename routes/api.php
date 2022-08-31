<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Log;
 
Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
  
Route::group(['middleware' => 'jwt.auth'], function () {
    //user Api's
    Route::post('logout', [UserController::class, 'logout']);
    Route::get('getUser', [UserController::class, 'getUser']);
    //profile Api's
    Route::post('createProfile', [ProfileController::class, 'create']);
    Route::get('getProfile', [ProfileController::class, 'get']);
    Route::post('updateProfile', [ProfileController::class, 'update']);  
    //post Api's
    Route::post('createPost', [PostController::class, 'create']);
    Route::get('getPost', [PostController::class, 'get']);
    Route::post('updatePost', [PostController::class, 'update']);
    Route::post('deletePost', [PostController::class, 'delete']);
    Route::get('getAllPosts', [PostController::class, 'getAll']);
    //comment Api's
    Route::post('createComment', [CommentController::class, 'create']);
    Route::get('getAllComment', [CommentController::class, 'getAll']);
});
