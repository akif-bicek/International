<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\GenderController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\OperationCategoryController;
use App\Http\Controllers\DoctorController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
*/

Route::middleware('Api')->group(function ()
{
    Route::post('/user', function (){
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user != null)
        {
            $user->is_developer = 0;
            if ($user->is_admin == 2)
            {
                $user->is_developer = 1;
            }
            $user->photo = public_path('uploads/images/') . $user->photo;
            $user->gender = single('gender', ['id' => $user->gender])->name;
        }
        return screaming('user', $user ?? []);
    });

    Route::post('/response', [AppController::class, 'response']);
    Route::post('/app', [AppController::class, 'read']);
    Route::post('/app/update', [AppController::class, 'update']);

    Route::prefix('select')->group(function ()
    {
        Route::post('/genders', [GenderController::class, 'read']);
    });

    Route::prefix('auth')->group(function ()
    {
        Route::post('/', [AuthController::class, 'read']);
        Route::post('/create', [AuthController::class, 'create']);
        Route::post('/update', [AuthController::class, 'update']);

        Route::post('/password', [AuthController::class, 'password']);

        Route::post('/reset-phone', [AuthController::class, 'resetPhone']);
        Route::post('/reset-code', [AuthController::class, 'resetCode']);
        Route::post('/reset-phone', [AuthController::class, 'resetPassword']);

        Route::post('photo', [AuthController::class, 'photo']);
        Route::post('photo-reset', [AuthController::class, 'photoReset']);
    });

    Route::prefix('operations')->group(function ()
    {
        Route::post('/', [OperationController::class, 'read']);
        Route::post('/{id}', [OperationController::class , 'read']);
    });

    Route::prefix('categories')->group(function ()
    {
        Route::post('/operation' , [OperationCategoryController::class, 'read']);
    });

    Route::prefix('doctors')->group(function ()
    {
        Route::post('/', [DoctorController::class, 'read']);
        Route::post('/{id}', [DoctorController::class, 'read']);
    });
});
