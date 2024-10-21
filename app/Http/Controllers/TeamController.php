<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function show ($program_id)
    {
        $program = Program::find($program_id);
        $teamMember = $program->users;
        return response([
            'member' => $teamMember,
        ], 200 );
    }
    
    public function store(Request $request, $program_id)
    {
    $program = Program::find($program_id);
    
    // Pastikan jumlah id dan role sama
    if (count($request->id) !== count($request->role)) {
        return response([
            'message' => 'Jumlah user dan role harus sama.',
        ], 400);
    }
    
    foreach ($request->id as $index => $user_id) {
        $user = User::find($user_id);
        
        $role = $request->role[$index];

        if ($user) {
            $user->programs()->attach($program_id, ['role' => $role]);
        } else {
            return response([
                'message' => "User dengan ID {$user_id} tidak ditemukan.",
            ], 404);
        }
    }
    
    return response([
        'message' => 'Berhasil menambahkan anggota tim',
    ], 200);
    }

    
    public function destroy (Request $request, $program_id)
    {
        $program = Program::find($program_id);
        foreach($request->id as $user_id){
            $user = User::find($user_id);
            $user->programs()->detach($program_id);
        }
        return response([
            'message' => 'Berhasil menghapus anggota tim',
        ], 200 );
    }
}
