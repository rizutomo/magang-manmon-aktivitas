<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Program;
use Illuminate\Support\Facades\Validator;
use App\Enums\ReportStatus;
use Response;


class ProgramController extends Controller
{
    public function programWithProgress()
    {
        $programs = Program::with(['tasks.users' => function ($query) {
            $query->withPivot('status');
        }])->get();

        // dd($programs);

        $programsData = $programs->map(function ($program) {
            $totalTasks = $program->tasks->count();

            $completedTasks = $program->tasks->filter(function ($task) {
                return $task->users->every(function ($user) {
                    return $user->pivot->status === ReportStatus::Diterima->value;
                });
            })->count();

            $coordinator = $program->users->filter(function ($user) {
                return $user->pivot->role === 'koordinator';
            })->pluck('name')->first();

            return [
                'id' => $program->id,
                'name' => $program->name,
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
    public function index()
    {
        $programs = Program::with('tasks')->get();
        if(!$programs){
            return response([
                'message' => 'Program tidak ditemukan'
            ], 404);
        }

        return response([
            'programs' =>$programs
        ], 200);
    }
    public function getByUserId(Request $request)
    {
        $user = $request->user(); 
        $programs = $user->programs()->get();
        $totalProgram = $programs->count();

        return response([
            'programs' => $programs,
            'total' => $totalProgram
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

        $program = New Program();
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
        $program = Program::find($id);
        if(!$program){
            return response([
                'message' => 'Program tidak ditemukan'
            ], 404);
        }

        return response([
            'program' => $program,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'sector_id' => 'required|string',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);
        
        $program = Program::find($id);

        if(!$program){
            return response([
                'message' => 'Program tidak ditemukan'
            ], 404);
        }

        $program->name = $request->name;
        $program->sector_id = $request->sector_id;
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
        
        if(!$program){
            return response([
                'message' => 'Program tidak ditemukan'
            ], 404);
        }

        $program->delete();
        return response([
            'message' => 'Program berhasil dihapus'
        ], 204);
    }
}
