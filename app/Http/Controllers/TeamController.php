<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\User;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function show($program_id)
    {
        $program = Program::find($program_id);
        if(!$program) {
            return response([
                'message' => 'Program tidak ditemukan',
            ], 200);
        }
        $teamMember = $program->users;
        return response([
            'team' => $teamMember,
        ], 200);
    }

    public function store(Request $request, string $program_id)
    {
        // if (count($request->id) !== count($request->role)) {
        //     return response([
        //         'message' => 'Jumlah user dan role harus sama.',
        //     ], 400);
        // }

        // foreach ($request->id as $index => $user_id) {
        $user = User::find($request->id);
        $role = $request->role;

        if ($user) {
            $user->programs()->attach($program_id, ['role' => $role]);
        } else {
            return response([
                'message' => "User dengan ID {$request->id} tidak ditemukan.",
            ], 404);
        }
        // }

        return response([
            'message' => 'Berhasil menambahkan anggota tim',
        ], 200);
    }
    
    public function storeMany(Request $request, string $program_id)
    {
        if (count($request->id) !== count($request->role)) {
            return response([
                'message' => 'Jumlah user dan role harus sama.',
            ], 400);
        }

        foreach ($request->id as $index => $user_id) {
        $user = User::find($request->id);
        $role = $request->role;

        if ($user) {
            $user->programs()->attach($program_id, ['role' => $role]);
        } else {
            return response([
                'message' => "User dengan ID {$request->id} tidak ditemukan.",
            ], 404);
        }
        }

        return response([
            'message' => 'Berhasil menambahkan anggota tim',
        ], 200);
    }


    public function update(Request $request, $program_id)
    {
        $user = Team::where('user_id', $request->user_id)->where('program_id', $program_id)->first();
        if(!$user){
            return response([
                'message' => 'Anggota tim tidak ditemukan'
            ], 404);
        }
        $user->role = $request->role;
        $user->save();
        return response([
            'message' => 'Berhasil mengedit anggota tim',
        ], 200);
    }

    public function destroy(Request $request, $program_id)
    {
        $user = User::find($request->user_id);
        if(!$user){
            return response([
                'message' => 'Anggota tim tidak ditemukan'
            ], 404);
        }
        $user->programs()->detach($program_id);
        return response([
            'message' => 'Berhasil menghapus anggota tim',
        ], 204);
    }
}
