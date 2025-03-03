<?php

use App\Http\Controllers\SectorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FileController;

Route::get('storage/reportdocs/{uuid}/{filename}', [FileController::class, 'getFile']);
Route::get('storage/reportfoto/{uuid}/{filename}', [FileController::class, 'getFoto']);
Route::post('login', [AuthController::class, 'login']);
Route::post('send-reset-code', [AuthController::class, 'sendResetCode']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);
Route::middleware('auth:sanctum')->get('user/profile', [AuthController::class, 'getProfile']);
Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('role:admin')->group(function () {
        Route::get('user', [AuthController::class, 'getAllUser']);
        Route::get('user/count', [AuthController::class, 'getUserCount']);

        //ADMIN

        
    });
    Route::middleware('role:admin|supervisor')->group(function () {
        //SUPERVISOR
        Route::post('program', [ProgramController::class, 'store'])->name('program.store');
        Route::put('program/{id}', [ProgramController::class, 'update'])->name('program.update');
        Route::delete('program/{id}', [ProgramController::class, 'destroy'])->name('program.destroy');
        Route::get('program/count', [ProgramController::class, 'getProgramCount'])->name('program.count');
        Route::get('program/ended/count', [ProgramController::class, 'getProgramEndedCount'])->name('program.endedCount');
        Route::get('program/upcoming', [ProgramController::class, 'upcomingPrograms'])->name('program.upcoming');
        Route::get('program/in-progress/count', [ProgramController::class, 'getProgramInProgressCount'])->name('program.inProgressCount');
        Route::get('program', [ProgramController::class, 'index'])->name('program.index');
        Route::get('task/upcoming', [TaskController::class, 'upcomingTasks'])->name('task.upcoming');
        Route::get('task/count', [TaskController::class, 'getTaskCount'])->name('task.getTaskCount');
        Route::get('sector', [ProgramController::class, 'getSector'])->name('sector.get');
        Route::get('sectorUser', [ProgramController::class, 'getUserSector'])->name('sector.get');
        Route::get('programProgress', [ProgramController::class, 'programWithProgress'])->name('program.index');
        Route::get('task/count/sector', [TaskController::class, 'getTaskCountBySector'])->name('task.getTaskCount'); 
        Route::get('program/ended/count/sector', [ProgramController::class, 'getEndedProgramCountBySector'])->name('program.endedCountSector');
        Route::get('program/count/sector', [ProgramController::class, 'getProgramCountBySector'])->name('program.countsector');
        Route::post('task', [TaskController::class, 'store'])->name('task.store');
        Route::put('task/{id}', [TaskController::class, 'update'])->name('task.update');
        Route::delete('task/{id}', [TaskController::class, 'destroy'])->name('task.destroy');
        Route::get('program/sector', [ProgramController::class, 'getBySector'])->name('program.getBySector');
        Route::get('program/upcoming/sector', [ProgramController::class, 'upcomingProgramsBySector'])->name('program.upcomingSector');
        Route::get('getUsersBySector/{id}/', [SectorController::class, 'getUsersBySector'])->name('sector.getUsersBySector');
        Route::get('program/in-progress/sector', [ProgramController::class, 'getProgramSectorProgress'])->name('program.sectorprogress');
        Route::post('program-{program_id}/team', [TeamController::class, 'store'])->name('team.store');
        Route::post('program-{program_id}/team/many/', [TeamController::class, 'storeMany'])->name('team.store');
        Route::put('program-{program_id}/team', [TeamController::class, 'update'])->name('team.store');
        Route::delete('program-{program_id}/team', [TeamController::class, 'destroy'])->name('team.store');
        Route::post('task/{id}/attachTeam', [TaskController::class, 'attachTeam'])->name('task.attachTeam');
        Route::delete('task/{id}/detachTeam', [TaskController::class, 'detachTeam'])->name('task.detachTeam');
        Route::get('task', [TaskController::class, 'indexall'])->name('task.indexall');
        Route::get('task/sector', [TaskController::class, 'getTaskBySector'])->name('task.indexSector');
        Route::get('task/sector/upcoming', [TaskController::class, 'upcomingTaskBySector'])->name('task.indexSectorUpcoming');
        Route::get('report/{task_id}', [ReportController::class, 'index'])->name('report.indexbytask');
        Route::post('report/{report_id}/status', [ReportController::class, 'updateCommentAndStatus'])->name('report.status');
        
    });
    Route::middleware('role:admin|supervisor|user')->group(function () {
        //USER
        Route::get('reportMobile/{task_id}', [ReportController::class, 'index2'])->name('report.indexbytask');
        Route::get('report/{task_id}', [ReportController::class, 'index'])->name('report.indexbytask');
        Route::get('task/count/user', [TaskController::class, 'getTaskCountByUser'])->name('task.getTaskCount');
        Route::get('program/count/user', [ProgramController::class, 'getProgramCountByUser'])->name('program.sectorCount');
        Route::get('user/program/', [ProgramController::class, 'getByUserId'])->name('program.getByUserId');
        Route::get('user/program/upcoming', [ProgramController::class, 'upcomingProgramsByUser'])->name('program.getByUserIdUpcoming');
        Route::get('user/program/in-progress', [ProgramController::class, 'getInProgressUserProgramCount'])->name('program.getByUserIdInProgress');
        Route::get('user/program/ended', [ProgramController::class, 'getEndedUserProgramCount'])->name('program.getByUserIdEnd');
        Route::get('count/program', [ProgramController::class, 'getTotalByUser'])->name('program.getTotalByUser');
        Route::get('program-{program_id}/task', [TaskController::class, 'index'])->name('task.index');
        Route::get('program/{program_id}/team', [TeamController::class, 'show'])->name('team.show');
        Route::get('user/task', [TaskController::class, 'getByUserId'])->name('task.getByUserId');
        Route::get('user/program/{program_id}/task', [TaskController::class, 'getByUserProgram'])->name('task.getByUserProgram');
        Route::get('user/task/upcoming', [TaskController::class, 'upcomingTaskByUser'])->name('task.getByUserIdUpcoming');
        Route::get('count/task', [TaskController::class, 'getTotalbyUser'])->name('task.getTotalbyUser');
        Route::get('task/{id}', [TaskController::class, 'show'])->name('task.show');
        Route::get('task-{id}/team', [TaskController::class, 'getTaskTeam'])->name('task.getTaskTeam');
        Route::get('task-{task_id}/team2', [ReportController::class, 'index'])->name('task.getTaskTeam');
        Route::post('report', [ReportController::class, 'store'])->name('report.store');
        Route::post('reportMobile', [ReportController::class, 'storeMobile'])->name('report.storeMobile');
        Route::delete('report/{report_id}', [ReportController::class, 'destroy'])->name('report.delete');
        Route::put('report/{report_id}', [ReportController::class, 'update'])->name('report.update');
        //AUTH
        Route::post('register', [AuthController::class, 'register']);
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('validate-token', [AuthController::class, 'validateToken']);
        Route::get('program/{id}/', [ProgramController::class, 'show'])->name('program.show');
    });
});
