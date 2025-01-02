<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Report;
use App\Models\ReportFile;
use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class ReportController extends Controller
{
    public function index($task_id)
    {
        $task = Task::findOrFail($task_id);

        // Ambil program yang berelasi dengan task
        $program = $task->program;

        if (!$program) {
            return response([
                'message' => 'Program tidak ditemukan untuk task ini.',
            ], 404);
        }

        // Ambil semua user yang berelasi dengan task
        $teamMembers = $task->users->map(function ($user) use ($program, $task) {
            // Cari role user dalam program melalui tabel pivot
            $role = $program->users()->where('users.id', $user->id)->first()->pivot->role ?? null;
            $report = $task->report;
            return [
                'user' => $user,
                'report' => $report,
                'role' => $role,
            ];
        });

        return response([
            'team_members' => $teamMembers,
        ], 200);
    }


    // public function show($program_id)
    // {
    //     $program = Program::find($program_id);
    //     if (!$program) {
    //         return response([
    //             'message' => 'Program tidak ditemukan',
    //         ], 200);
    //     }
    //     $teamMember = $program->users()->with('occupation')->get();
    //     return response([
    //         'team' => $teamMember,
    //     ], 200);
    // }

    public function update($program_id)
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


    public function store(Request $request)
    {
        $user_id = auth()->user()->id;
        $task_id = $request->input('task_id');
        $user = User::findOrFail($user_id);
        $task = Task::findOrFail($task_id);

        $request->validate([
            'date' => 'nullable|date',
            'time' => 'nullable|date_format:H:i',
            'description' => 'nullable|string',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'photo' => 'nullable|mimes:jpg,png|max:61440',
            'documents.*' => 'nullable|mimes:pdf,doc,docx|max:20480',
        ], [
            'photo.required' => 'Harap tambahkan foto laporan anda',
            'photo.mimes' => 'Tipe file foto tidak valid',
            'photo.max' => 'Ukuran foto terlalu besar. Max : 60 MB',
            'documents.*.mimes' => 'Tipe file dokumen tidak valid',
            'documents.*.max' => 'Ukuran dokumen terlalu besar. Max : 20 MB',
        ]);

        $existingReport = Report::where('task_id', $task_id)->first();

        if ($existingReport) {
            // Menghapus foto lama jika ada
            if ($existingReport->photo && Storage::disk('public')->exists($existingReport->photo)) {
                Storage::disk('public')->delete($existingReport->photo);
            }

            // Simpan foto baru jika ada
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $photoPath = $photo->store('reportfoto/' . $task->id, 'public');
            } else {
                $photoPath = null;
            }

            // Update data pada tabel `reports`

            $existingReport->photo = $photoPath;
            $existingReport->date = $request->input('date');
            $existingReport->desctiption = $request->input('description');
            $existingReport->latitude = $request->input('latitude');
            $existingReport->longitude = $request->input('longitude');

            // $user->tasks()->updateExistingPivot($task_id, [
            //     'photo' => $photoPath,
            //     'date' => $request->input('date'),
            //     'description' => $request->input('description'),
            //     'latitude' => $request->input('latitude'),
            //     'longitude' => $request->input('longitude'),
            // ]);


            // Simpan dokumen jika ada
            if ($existingReport && $request->hasFile('documents')) {
                foreach ($request->file('documents') as $document) {
                    $documentPath = $document->store('reportdocs/' . $task->id, 'public');
                    ReportFile::create([
                        'report_id' => $existingReport->id,
                        'name' => $documentPath,
                    ]);
                }
            }

            return response()->json(['success' => 'Pengumpulan berhasil diperbarui'], 200);
        }

        return response()->json(['error' => 'Pengumpulan gagal'], 404);
    }


    public function destroy($report_id)
    {
        $user = Auth::user();
        $report = Report::where('id', $report_id)->first();

        if ($report) {
            $task_id = $report->task_id;
            $existingReport = Report::where('task_id', $task_id)->first();

            if ($existingReport) {
                // Delete the associated photo if it exists
                if ($existingReport->file && Storage::disk('local')->exists($existingReport->file)) {
                    Storage::disk('local')->delete($existingReport->file);
                }

                // Empty data in the `reports` table without deleting the entry
                // $user->tasks()->updateExistingPivot($task_id, [
                //     'photo' => null,
                //     'date' => null,
                //     'description' => null,
                //     'latitude' => null,
                //     'longitude' => null,
                // ]);

                // Delete all document files related to the report in `report_file`
                $reportFiles = ReportFile::where('report_id', $report->id)->get();

                foreach ($reportFiles as $reportFile) {
                    if (Storage::disk('local')->exists($reportFile->name)) {
                        Storage::disk('local')->delete($reportFile->name);
                    }
                    // Delete the report file record
                    $reportFile->delete();
                }

                $existingReport->delete();

                return response()->json(['success' => 'Data berhasil dihapus'], 200);
            }
        }

        return response()->json(['error' => 'Pengumpulan tidak ditemukan'], 404);
    }


}





