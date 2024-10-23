<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Program;
use Illuminate\Support\Facades\Validator;


class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::all();
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

        return response([
            'programs' => $programs
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
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
        $program->supervisor_id = $request->user()->id;
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
            'supervisor_id' => 'required|string',
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
        $program->supervisor_id = $request->supervisor;
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
