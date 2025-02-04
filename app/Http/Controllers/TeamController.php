<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Task;
use App\Models\User;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function show($program_id)
    {
        $program = Program::find($program_id);
        if (!$program) {
            return response([
                'message' => 'Program tidak ditemukan',
            ], 200);
        }
        $teamMember = $program->users()->with('occupation')->get();
        return response([
            'team' => $teamMember,
        ], 200);
    }

    public function store(Request $request, string $program_id)
    {

        $user = User::find($request->id);
        $role = 'ketua';

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

        foreach ($request->id as $index => $user_id) {
            $user = User::find($user_id);
            $role = 'anggota';

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

    public function storeMany2(Request $request, string $program_id)
    {

        $dataToSync = [];
        foreach ($request->id as $index => $user_id) {
            $dataToSync[$user_id] = ['role' => $request->role[$index]];
        }

        $program = Program::find($program_id);
        if ($program) {
            $program->users()->syncWithoutDetaching($dataToSync);

            return response([
                'message' => 'Berhasil menambahkan anggota tim.',
            ], 200);
        }

        return response([
            'message' => "Program dengan ID {$program_id} tidak ditemukan.",
        ], 404);
    }


    public function update(Request $request, $program_id)
    {
        $user = Team::where('user_id', $request->user_id)->where('program_id', $program_id)->first();
        if (!$user) {
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
    $program = Program::find($program_id);

    if (!$user) {
        return response([
            'message' => 'Anggota tim tidak ditemukan'
        ], 404);
    }

    if (!$program) {
        return response([
            'message' => 'Program tidak ditemukan'
        ], 404);
    }

    // Detach user dari program
    $user->programs()->detach($program_id);

    // Ambil semua tasks yang berelasi dengan program
    $tasks = $program->tasks; 

    // Loop setiap task dan detach user
    foreach ($tasks as $task) {
        $user->tasks()->detach($task->id);
    }

    return response([
        'message' => 'Berhasil menghapus anggota tim dari program dan tugas terkait',
    ], 204);
}

}
