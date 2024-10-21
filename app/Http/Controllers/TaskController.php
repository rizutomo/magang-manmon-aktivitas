<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function index($programID)
    {
        $tasks = Task::where('program_id', $programID)->get();
        return response([
            'tasks' => $tasks,
        ], 200);
    }

    public function store(Request $request)
    {
        $task = New Task();
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
        $task = Task::find($id);
        return response([
            'task' => $task,
        ], 200);
    }

    public function update(Request $request, string $id)
    {
        $task = Task::find($id);
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
        $task->delete();
        return response([
            'message' => 'Kegiatan berhasil terhapus',
        ], 200);
    }
}
