<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
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
        $tasks = $user->tasks()->with('report')->get();
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
        'file' => 'nullable|mimes:pdf,doc,docx,jpg,png|max:2048', // Maksimum ukuran file 2MB
    ], [
        'file.mimes' => 'Tipe file tidak valid',
        'file.max' => 'Ukuran file maksimum adalah 2MB',
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

    if ($request->hasFile('file')) {
        // Simpan file ke folder storage/app/public/taskfiles
        $filePath = $request->file('file')->store('taskfiles', 'public');
        $task->file = basename($filePath); // Simpan nama file saja ke database
    }

    $task->save();

    return response()->json([
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

    if (!$task) {
        return response([
            'message' => 'Kegiatan tidak ditemukan'
        ], 404);
    }

    if ($task->file) {
        $task->file = asset('storage/taskfiles/' . $task->file);
    }

    return response([
        'task' => $task,
        'task_users' => $task->users,
        'program_users' => $task->program->users ?? []
    ], 200);
}




    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'program_id' => 'required|string',
            'name' => 'required|string',
            'host' => 'required|string',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'description' => 'required|string',
            'file' => 'nullable|mimes:pdf,doc,docx,jpg,png|max:2048', // Maksimum ukuran file 2MB
        ], [
            'file.mimes' => 'Tipe file tidak valid',
            'file.max' => 'Ukuran file maksimum adalah 2MB',
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
            return response()->json([
                'success' => false,
                'message' => 'Kegiatan tidak ditemukan',
            ], 404);
        }
    
        $task->program_id = $request->program_id;
        $task->name = $request->name;
        $task->host = $request->host;
        $task->date = $request->date;
        $task->time = $request->time;
        $task->location = $request->location;
        $task->description = $request->description;
    
        if ($request->hasFile('file')) {
            // Hapus file lama jika ada
            if ($task->file) {
                $oldFilePath = public_path('storage/taskfiles/' . $task->file);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath); 
                }
            }
    
            // Simpan file baru
            $filePath = $request->file('file')->store('taskfiles', 'public');
            $task->file = basename($filePath); 
        }
    
        $task->save();
    
        return response()->json([
            'success' => true,
            'message' => 'Kegiatan berhasil diperbarui',
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

        if ($task->file) {
            $filePath = public_path('storage/taskfiles/' . $task->file);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        $task->delete();
        return response([
            'message' => 'Kegiatan berhasil terhapus',
        ], 204);
    }

    public function attachTeam(Request $request, $id)
    {
        // // $task = Task::find($id);
        // $user = User::find($request->user_id);
        // if (!$user) {
        //     return response([
        //         'message' => 'User tidak ditemukan'
        //     ], 404);
        // };

        foreach ($request->id as $index => $user_id) {
            $user = User::find($user_id);
            // $role = 'anggota';

            if ($user) {
                $user->tasks()->attach($id, ['id' => Str::uuid()]);
            } else {
                return response([
                    'message' => "User dengan ID {$request->id} tidak ditemukan.",
                ], 404);
            }
        }

        // if ($user) {
        //     $user->tasks()->attach($id, ['id' => Str::uuid()]);
        // } else {
        //     return response([
        //         'message' => "User dengan ID {$request->id} tidak ditemukan.",
        //     ], 404);
        // }

        return response([
            'message' => 'Berhasil menambahkan anggota tim ke dalam kegiatan',
            'name' => $user->name
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

    public function getTaskTeam(Request $request, $id)
    {
        // $task = Task::find($id);
        $task = Task::find($id);
        if (!$task) {
            return response([
                'message' => 'Kegiatan tidak ditemukan'
            ], 404);
        }
        $users = $task->users;

        return response([
            'users' => $users
        ], 200);
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
