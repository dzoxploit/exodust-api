<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiGithubUserController;
use App\Http\Controllers\GithubUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SearchLogController;

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



//search
Route::get('/index-github-users-saved',  [GithubUserController::class, 'index'])->middleware('auth:api');
Route::get('/index-search-logs',  [SearchLogController::class, 'index'])->middleware('auth:api');

//CRUD
Route::post('/index-github-users/auth',  [ApiGithubUserController::class, 'index_github_main_auth']);
Route::get('/index-github-users-saved',  [GithubUserController::class, 'index'])->middleware('auth:api');
Route::post('/insert-github-users',  [ApiGithubUserController::class, 'insert_data_github_users'])->middleware('auth:api');
Route::get('/searching-github-users-saved',  [GithubUserController::class, 'searching'])->middleware('auth:api');
Route::post('/update-github-users/{id}', [GithubUserController::class, 'update'])->middleware('auth:api');
Route::post('/delete-github-users/{id}', [GithubUserController::class, 'delete'])->middleware('auth:api');

//list organizations

Route::post('/list_organization_repositories',  [ApiGithubUserController::class, 'list_organization_repositories_github_main']);
Route::post('/create_organization_repositories',  [ApiGithubUserController::class, 'create_organization_repositories_github_main']);
Route::post('/get_organization_repositories',  [ApiGithubUserController::class, 'get_organization_repositories_github_main']);
Route::post('/update_organization_repositories',  [ApiGithubUserController::class, 'update_organization_repositories_github_main']);
Route::post('/delete_organization_repositories',  [ApiGithubUserController::class, 'delete_organization_repositories_github_main']);
Route::post('/enable_automated_fixes_organization_repositories',  [ApiGithubUserController::class, 'enable_automated_fixes_organization_repositories_github_main']);
Route::post('/disable_automated_fixes_organization_repositories',  [ApiGithubUserController::class, 'disable_automated_fixes_organization_repositories_github_main']);


//Login, Register, Index no auth
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register',[App\Http\Controllers\AuthController::class, 'register']);
Route::post('/index-github-users',  [ApiGithubUserController::class, 'index_github_main']);