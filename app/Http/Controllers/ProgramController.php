<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Program;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::all();

        return response([
            'programs' =>$programs
        ], 200);
    }

    public function store(Request $request)
    {
        $program = New Program();
        $program->name = $request->name;
        $program->supervisor_id = $request->supervisor_id;
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

        return response([
            'program' => $program,
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $program = Program::find($id);

        return response([
            'program' =>$program
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $program = Program::find($id);
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
        $program->delete();
        return response([
            'message' => 'Program berhasil dihapus'
        ], 200);
    }
}
