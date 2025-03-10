<?php

namespace App\Http\Controllers;

use App\Models\Sector;
use Illuminate\Http\Request;
use App\Models\Program;
use Illuminate\Support\Facades\Validator;
use App\Enums\ReportStatus;
use Response;
use Carbon\Carbon;


class ProgramController extends Controller
{
    public function programWithProgress()
    {
        $programs = Program::with([
            'sector',
            'tasks.users',
            'tasks.report'
        ])->get();
    
        $programsData = $programs->map(function ($program) {
            $totalTasks = $program->tasks->count();
    
            $completedTasks = $program->tasks->filter(function ($task) {
                return $task->report && $task->report->status?->value === 'Diterima';
            })->count();
    
            $coordinator = $program->users->filter(function ($user) {
                return $user->pivot->role === 'ketua';
            })->first();
    
            return [
                'id' => $program->id,
                'name' => $program->name,
                'sector' => $program->sector,
                'start_date' => $program->start_date,
                'end_date' => $program->end_date,
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'description' => $program->description,
                'coordinator' => $coordinator,
            ];
        });
    
        return response([
            'programs' => $programsData
        ], 200);
    }

    public function getSector()
    {
        $sectors = Sector::with('user')->get();
        // dd($sectors);

        return response([
            'sectors' => $sectors
        ], 200);
    }

    public function getUserSector()
    {
        $user = auth()->user();
    
        $sector = Sector::with('user') 
            ->where('id', $user->sector_id) 
            ->first();
    
        if (!$sector) {
            return response()->json([
                'message' => 'Sector tidak ditemukan untuk user ini'
            ], 404);
        }
    
        return response()->json([
            'sector' => $sector
        ], 200);
    }
    

    public function index()
    {
        $programs = Program::with('tasks', 'users')->get();
        if (!$programs) {
            return response([
                'message' => 'Program tidak ditemukan'
            ], 404);
        }

        return response([
            'programs' => $programs
        ], 200);
    }

    public function getProgramCount()
    {
        $count = Program::count();
        return response()->json(['count' => $count]);
    }
    public function getProgramCountByUser()
    {
        $user = auth()->user();
        $count = $user->programs->count();
        return response()->json(['count' => $count]);
    }
    public function getByUserId(Request $request)
    {
        $user = auth()->user();
        // dd($user);
        $programs = $user->programs()->with([
            'sector',
            'tasks.users',
            'tasks.report'
        ])->get();

        // dd($programs);

        $programsData = $programs->map(function ($program) {
            $totalTasks = $program->tasks->count();

            $completedTasks = $program->tasks->filter(function ($task) {
                return $task->report && $task->report->status === ReportStatus::Diterima->value;
            })->count();

            $coordinator = $program->users->filter(function ($user) {
                return $user->pivot->role === 'ketua';
            })->first();

            return [
                'id' => $program->id,
                'name' => $program->name,
                'sector' => $program->sector,
                'start_date' => $program->start_date,
                'end_date' => $program->end_date,
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'description' => $program->description,
                'coordinator' => $coordinator,
            ];
        });

        return response([
            'programs' => $programsData
        ], 200);
    }

    public function getTotalbyUser(Request $request)
    {
        $totalProgram = $request->user()->programs()->count();

        return response([
            'countProgram' => $totalProgram
        ], 200);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'sector_id' => 'required|string',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $program = new Program();
        $program->name = $request->name;
        $program->sector_id = $request->sector_id;
        $program->description = $request->description;
        $program->start_date = $request->start_date;
        $program->end_date = $request->end_date;
        $program->save();

        return response([
            'message' => 'Program berhasil ditambahkan',
            'program' => $program,
        ], 201);
    }

    public function show(string $id)
    {
        $program = Program::with(['tasks.users', 'users', 'sector'])->find($id);

        if (!$program) {
            return response([
                'message' => 'Program tidak ditemukan'
            ], 404);
        }

        return response([
            'program' => $program
        ], 200);
        return response([
            'program' => $program,
            'anggota' => $program->users->map(function ($user) {
                return [
                    'users' => $user->name,
                    'value' => $user->id,
                ];
            })
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $program = Program::find($id);

        if (!$program) {
            return response([
                'message' => 'Program tidak ditemukan'
            ], 404);
        }

        $program->name = $request->name;
        $program->description = $request->description;
        $program->start_date = $request->start_date;
        $program->end_date = $request->end_date;
        $program->save();

        return response([
            'message' => 'Program berhasil diedit',
            'program' => $program,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $program = Program::find($id);

        if (!$program) {
            return response([
                'message' => 'Program tidak ditemukan'
            ], 404);
        }

        $program->delete();
        return response([
            'message' => 'Program berhasil dihapus'
        ], 204);
    }

    public function getProgramInProgressCount()
    {
        $count = Program::where('end_date', '>', Carbon::now())->count();

        return response()->json(['count' => $count]);
    }

    public function getProgramEndedCount()
    {
        $count = Program::where('end_date', '<', Carbon::now())->count();

        return response()->json(['count' => $count]);
    }
    public function upcomingPrograms()
    {
        $today = Carbon::today();
        $programs = Program::with('tasks')
            ->where('end_date', '>=', $today)
            ->orderBy('start_date', 'asc')
            ->get();

        if ($programs->isEmpty()) {
            return response()->json([
                'message' => 'Program tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'programs' => $programs
        ], 200);
    }

    public function upcomingProgramsBySector()
    {
        $user = auth()->user();
        $sector = $user->sector;
        $today = Carbon::today();

        $programs = $sector->programs()
            ->with('tasks')
            ->where('end_date', '>=', $today)
            ->orderBy('start_date', 'asc')
            ->get();

        if ($programs->isEmpty()) {
            return response()->json([
                'message' => 'Program tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'programs' => $programs
        ], 200);
    }

    public function upcomingProgramsByUser()
    {
        $user = auth()->user();
        $today = Carbon::today();

        // Ambil program mendatang berdasarkan pengguna
        $programs = $user->programs()->with([
            'sector',
            'tasks.users',
            'tasks.report'
        ])->where('end_date', '>=', $today)
            ->orderBy('start_date', 'asc')
            ->get();

        if ($programs->isEmpty()) {
            return response()->json([
                'message' => 'Program tidak ditemukan'
            ], 404);
        }

        $programsData = $programs->map(function ($program) {
            $totalTasks = $program->tasks->count();

            $completedTasks = $program->tasks->filter(function ($task) {
                return $task->report && $task->report->status === ReportStatus::Diterima->value;
            })->count();

            $coordinator = $program->users->filter(function ($user) {
                return $user->pivot->role === 'ketua';
            })->first();

            return [
                'id' => $program->id,
                'name' => $program->name,
                'sector' => $program->sector,
                'start_date' => $program->start_date,
                'end_date' => $program->end_date,
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'description' => $program->description,
                'coordinator' => $coordinator,
            ];
        });

        return response()->json([
            'programs' => $programsData
        ], 200);
    }



    public function getBySector()
    {
        $user = auth()->user();
        $sector = $user->sector;
        $programs = $sector->programs()->with([
            'sector',
            'tasks.users',
            'tasks.report'
        ])->get();

        // dd($programs);

        $programsData = $programs->map(function ($program) {
            $totalTasks = $program->tasks->count();

            $completedTasks = $program->tasks->filter(function ($task) {
                return $task->report && $task->report->status === ReportStatus::Diterima->value;
            })->count();

            $coordinator = $program->users->filter(function ($user) {
                return $user->pivot->role === 'ketua';
            })->first();

            return [
                'id' => $program->id,
                'name' => $program->name,
                'sector' => $program->sector,
                'start_date' => $program->start_date,
                'end_date' => $program->end_date,
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'description' => $program->description,
                'coordinator' => $coordinator,
            ];
        });

        return response([
            'programs' => $programsData
        ], 200);
    }

    public function getProgramSectorProgress()
    {
        $user = auth()->user();
        $sector = $user->sector;

        // Hitung jumlah program yang sedang berjalan
        $count = $sector->programs()
            ->where('end_date', '>', Carbon::now())
            ->count();

        return response()->json(['count' => $count]);
    }
    public function getProgramCountBySector()
    {
        $user = auth()->user();
        $sector = $user->sector;


        $count = $sector->programs()->count();

        return response()->json(['count' => $count]);
    }

    public function getEndedProgramCountBySector()
    {
        $user = auth()->user();
        $sector = $user->sector;

        // Hitung jumlah program yang telah berakhir
        $count = $sector->programs()
            ->where('end_date', '<=', Carbon::now())
            ->count();

        return response()->json(['count' => $count]);
    }

    public function getEndedUserProgramCount()
    {
        $user = auth()->user();

        // Hitung program yang telah berakhir
        $count = $user->programs()
            ->where('end_date', '<=', Carbon::now())
            ->count();

        return response()->json(['count' => $count]);
    }

    public function getInProgressUserProgramCount()
    {
        $user = auth()->user();

        // Hitung program yang sedang berjalan
        $count = $user->programs()
            ->where('end_date', '>', Carbon::now())
            ->count();

        return response()->json(['count' => $count]);
    }
}
