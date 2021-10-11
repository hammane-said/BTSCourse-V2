<?php

use App\Http\Controllers\BranchsController;
use App\Http\Controllers\EstablishmentsController;
use App\Http\Controllers\FilesController;
use App\Http\Controllers\ModulesController;
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



Route::group(['middleware' => 'auth'], function () {
    Route::get('/', function () {
        return response()->json([
            'type' => 'success',
            'message' => 'BTS-Course',
        ]);
    });
    Route::get('/uncheckedUsers', [UsersController::class, 'uncheckedUsers']);
    Route::get('/structure', [UsersController::class, 'structure']);
    Route::get('/branches', [BranchsController::class, 'getBranches']);
    Route::post('/files/upload', [FilesController::class, 'upload']);
    Route::get('/files', [FilesController::class, 'index']);
    Route::get('/files/toggleConfirmation/{id}', [FilesController::class, 'toggleConfirmation']);
    Route::get('/files/delete/{id}', [FilesController::class, 'delete']);

    Route::get('/users/toggleConfirmation/{id}', [UsersController::class, 'toggleConfirmation']);
    Route::get('/users/delete/{id}', [UsersController::class, 'delete']);
    Route::get('/users', [UsersController::class, 'index']);
});


Route::get('/chart/{size?}', [FilesController::class, 'chart']);
Route::get('/checkEmail/{email}', [UsersController::class, 'checkEmail']);
Route::get('/login', [UsersController::class, 'login']);
Route::post('/register', [UsersController::class, 'register']);
Route::get('/establishments', [EstablishmentsController::class, 'getEstablishments']);
Route::get('/unusedModulesDetailsByEstablishment/{establishmentId}', [ModulesController::class, 'getUnusedModulesDetailsByEstablishment']);
Route::get('/branchsByEstablishment/{establishmentId}', [BranchsController::class, 'getBranchsByEstablishment']);
Route::get('/modulesByBranch/{branchId}', [ModulesController::class, 'getModulesByBranch']);
Route::get('/files/download/{id}', [FilesController::class, 'download']);
