<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;

Route::post('login', [AuthController::class, 'login']);
Route::post('send-reset-code', [AuthController::class, 'sendResetCode']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);

Route::get('user', [AuthController::class, 'getAllUser'])->middleware('auth:sanctum');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    //CRUD Program
    Route::get('program', [ProgramController::class, 'index'])->name('program.index');
    Route::get('program/{id}', [ProgramController::class, 'show'])->name('program.show');
    Route::post('program', [ProgramController::class, 'store'])->name('program.store');
    Route::put('program/{id}', [ProgramController::class, 'update'])->name('program.update');
    Route::delete('program/{id}', [ProgramController::class, 'destroy'])->name('program.destroy');
    
    //CRUD Task
    Route::get('program-{programID}/task', [TaskController::class, 'index'])->name('task.index');
    Route::get('task/{id}', [TaskController::class, 'show'])->name('task.show');
    Route::post('task', [TaskController::class, 'store'])->name('task.store');
    Route::put('task/{id}', [TaskController::class, 'update'])->name('task.update');
    Route::delete('task/{id}', [TaskController::class, 'destroy'])->name('task.destroy');
    
    //CRUD Team
    Route::get('program-{programID}/team', [TeamController::class, 'show'])->name('team.show');
    Route::post('program-{programID}/team', [TeamController::class, 'store'])->name('team.store');

});
