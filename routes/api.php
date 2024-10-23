<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\ReportController;

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
    Route::get('program/user/', [ProgramController::class, 'getByUserId'])->name('program.getByUserId');
    Route::post('program', [ProgramController::class, 'store'])->name('program.store');
    Route::put('program/{id}', [ProgramController::class, 'update'])->name('program.update');
    Route::delete('program/{id}', [ProgramController::class, 'destroy'])->name('program.destroy');
    
    //CRUD Task
    Route::get('program-{program_id}/task', [TaskController::class, 'index'])->name('task.index');
    Route::get('task/user/', [TaskController::class, 'getByUserId'])->name('task.getByUserId');
    Route::get('task/{id}', [TaskController::class, 'show'])->name('task.show');
    Route::post('task', [TaskController::class, 'store'])->name('task.store');
    Route::put('task/{id}', [TaskController::class, 'update'])->name('task.update');
    Route::delete('task/{id}', [TaskController::class, 'destroy'])->name('task.destroy');
    
    //CRUD Team
    Route::get('program-{program_id}/team', [TeamController::class, 'show'])->name('team.show');
    Route::post('program-{program_id}/team', [TeamController::class, 'store'])->name('team.store');
    Route::put('program-{program_id}/team', [TeamController::class, 'store'])->name('team.store');
    Route::delete('program-{program_id}/team', [TeamController::class, 'store'])->name('team.store');

});
