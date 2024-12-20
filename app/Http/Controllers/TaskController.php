<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function coba()
    {
        $tasks = Task::with('programs')->get();
        return response([
            'tasks' => $tasks,
        ], 200);
    }

    public function index($program_id)
    {
        $tasks = Task::where('program_id', $program_id)->get();
        return response([
            'tasks' => $tasks,
        ], 200);
    }
    public function indexall()
    {
        $tasks = Task::with('users')->get();
        if (!$tasks) {
            return response([
                'message' => 'Kegiatan tidak ditemukan'
            ], 404);
        }

        return response([
            'tasks' => $tasks
        ], 200);
    }

    public function getBySector(Request $request)
    {
        $user = $request->user();
        $tasks = $user->tasks()->get();
        $totalTask = $tasks->count();

        return response([
            'tasks' => $tasks,
            'total' => $totalTask
        ], 200);
    }
    public function getByUserId(Request $request)
    {
        $user = $request->user();
        $tasks = $user->tasks()->get();
        $totalTask = $tasks->count();

        return response([
            'tasks' => $tasks,
            'total' => $totalTask
        ], 200);
    }

    public function getTaskCount()
    {
        $count = Task::count();
        return response()->json(['count' => $count]);
    }

    public function getTotalbyUser(Request $request)
    {
        $totalTasks = $request->user()->tasks()->count();

        return response([
            'countTask' => $totalTasks
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'program_id' => 'required|string',
            'name' => 'required|string',
            'host' => 'required|string',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'description' => 'required|string',
            'file' => 'mimes:pdf,doc,docx,jpg,png',
        ], [
            'file.mimes' => 'Tipe file tidak valid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $task = new Task();
        $task->program_id = $request->program_id;
        $task->name = $request->name;
        $task->host = $request->host;
        $task->date = $request->date;
        $task->time = $request->time;
        $task->location = $request->location;
        $task->description = $request->description;
        $task->file = $request->file;
        $task->save();

        return response([
            'message' => 'Kegiatan berhasil ditambahkan',
            'task' => $task,
        ], 201);
    }

    public function show(string $id)
    {
        $task = Task::with([
            'users',
            'program.users' => function ($query) {
                $query->withPivot('role');
            }
        ])->find($id);
        $task = Task::find($id);

        if (!$task) {
            return response([
                'message' => 'Kegiatan tidak ditemukan'
            ], 404);
        }

        return response([
            'task' => $task,
            'task_users' => $task->users,
            'program_users' => $task->program->users ?? []
        ], 200);
    }



    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'program_id' => 'required|string',
            'name' => 'required|string',
            'host' => 'required|string',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'description' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,png',
        ], [
            'file.mimes' => 'Tipe file tidak valid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $task = Task::find($id);
        if (!$task) {
            return response([
                'message' => 'Kegiatan tidak ditemukan'
            ], 404);
        }

        $task->name = $request->name;
        $task->host = $request->host;
        $task->date = $request->date;
        $task->time = $request->time;
        $task->location = $request->location;
        $task->description = $request->description;
        $task->file = $request->file;
        $task->save();

        return response([
            'message' => 'Kegiatan berhasil diedit',
            'task' => $task,
        ], 200);
    }

    public function destroy(string $id)
    {
        $task = Task::find($id);
        if (!$task) {
            return response([
                'message' => 'Kegiatan tidak ditemukan'
            ], 404);
        }
        $task->delete();
        return response([
            'message' => 'Kegiatan berhasil terhapus',
        ], 204);
    }

    public function attachTeam(Request $request, $id)
    {
        // $task = Task::find($id);
        $user = User::find($request->user_id);
        if (!$user) {
            return response([
                'message' => 'User tidak ditemukan'
            ], 404);
        }
        ;
        $user->tasks()->attach($id, ['id' => Str::uuid()]);

        return response([
            'message' => 'Berhasil menambahkan anggota tim ke dalam kegiatan'
        ], 200);
    }

    public function detachTeam(Request $request, $id)
    {
        // $task = Task::find($id);
        $user = User::find($request->user_id);
        if (!$user) {
            return response([
                'message' => 'User tidak ditemukan'
            ], 404);
        }
        $user->tasks()->detach($id);

        return response([
            'message' => 'Berhasil menghapus anggota tim dari kegiatan'
        ], 204);
        ;
    }
    public function upcomingTasks()
    {
        $today = Carbon::today();
        $tasks = Task::with('users')
            ->where('date', '>=', $today)
            ->orderBy('date', 'asc')
            ->get();

        if ($tasks->isEmpty()) {
            return response()->json([
                'message' => 'task tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'tasks' => $tasks
        ], 200);
    }
}
