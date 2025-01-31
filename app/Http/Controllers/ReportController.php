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


        $report = $task->report;
        $files = $report->files;
        $user = $report->modifiedBy;


        return response([
            'report' => $report,
            'files' => $files,
            'user' => $user ? $user->name : null
        ], 200);
    }

    public function index2($task_id)
    {
        $task = Task::findOrFail($task_id);


        $report = $task->report;

        if ($report->photo) {
            $report->photo = asset('storage/' . $report->photo);
        }
        $files = $report->files;
        foreach ($files as $file) {

            if ($file->name) {
                $file->name = asset('storage/' . $file->name);
            }
        }

        $user = $report->modifiedBy;


        return response([
            'report' => $report,
            'files' => $files,
            'user' => $user ? $user->name : null
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


    public function store(Request $request)
    {
        $user_id = auth()->user()->id;
        $task_id = $request->input('task_id');

        // Validasi input
        $validatedData = $request->validate([
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

        // Cari laporan yang sudah ada berdasarkan task_id
        $report = Report::where('task_id', $task_id)->first();

        // Jika tidak ada, buat instance baru
        if (!$report) {
            $report = new Report();
            $report->task_id = $task_id;
        }

        // Perbarui/mengisi data laporan
        $report->date = $request->input('date', $report->date);
        $report->description = $request->input('description', $report->description);
        $report->latitude = $request->input('latitude', $report->latitude);
        $report->longitude = $request->input('longitude', $report->longitude);
        $report->modified_by = $user_id;

        // Proses upload foto jika ada
        if ($request->hasFile('photo')) {
            if ($report->photo && Storage::disk('public')->exists($report->photo)) {
                Storage::disk('public')->delete($report->photo);
            }
            $report->photo = $request->file('photo')->store('reportfoto/' . $task_id, 'public');
        }

        // Simpan laporan
        $report->save();
        //hapus dokumen lama
        if ($request->hasFile('documents')) {
            foreach ($report->files as $file) {
                if (Storage::disk('public')->exists($file->name)) {
                    Storage::disk('public')->delete($file->name);
                }
                $file->delete();
            }
        }

        // Proses upload dokumen jika ada
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                $documentPath = $document->store('reportdocs/' . $task_id, 'public');
                ReportFile::create([
                    'report_id' => $report->id,
                    'name' => $documentPath,
                ]);
            }
        }

        // Ambil laporan beserta file terkait untuk respon
        $report = Report::with('files')->find($report->id);

        return response()->json([
            'message' => $report->wasRecentlyCreated ? 'Laporan berhasil dibuat' : 'Laporan berhasil diperbarui',
            'data' => $report,
        ], 200);
    }
    public function storeMobile(Request $request)
    {
        $user_id = auth()->user()->id;
        $task_id = $request->input('task_id');

        // Validasi input
        $validatedData = $request->validate([
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

        // Cari laporan yang sudah ada berdasarkan task_id
        $report = Report::where('task_id', $task_id)->first();

        // Jika tidak ada, buat instance baru
        if (!$report) {
            $report = new Report();
            $report->task_id = $task_id;
        }

        // Perbarui/mengisi data laporan
        $report->date = $request->input('date', $report->date);
        $report->description = $request->input('description', $report->description);
        $report->latitude = $request->input('latitude', $report->latitude);
        $report->longitude = $request->input('longitude', $report->longitude);
        $report->modified_by = $user_id;

        // Proses upload foto jika ada
        if ($request->hasFile('photo')) {
            if ($report->photo && Storage::disk('public')->exists($report->photo)) {
                Storage::disk('public')->delete($report->photo);
            }
            $report->photo = $request->file('photo')->store('reportfoto/' . $task_id, 'public');
        }

        // Simpan laporan
        $report->save();

        if ($request->hasFile('documents')) {
            foreach ($report->files as $file) {
                if (Storage::disk('public')->exists($file->name)) {
                    Storage::disk('public')->delete($file->name);
                }
                $file->delete();
            }
        }

        // Proses upload dokumen jika ada
        if ($request->hasFile('documents')) {
            // foreach ($request->file('documents') as $document) {
            $documentPath = $request->file('documents')->store('reportdocs/' . $task_id, 'public');
            ReportFile::create([
                'report_id' => $report->id,
                'name' => $documentPath,
            ]);
            // }
        }

        // Ambil laporan beserta file terkait untuk respon
        $report = Report::with('files')->find($report->id);

        return response()->json([
            'message' => $report->wasRecentlyCreated ? 'Laporan berhasil dibuat' : 'Laporan berhasil diperbarui',
            'data' => $report,
        ], 200);
    }

    public function destroy($report_id)
    {
        $report = Report::find($report_id);
        if (!$report) {
            return response()->json(['error' => 'Laporan tidak ditemukan'], 404);
        }

        // Hapus foto laporan jika ada
        if ($report->photo && Storage::disk('public')->exists($report->photo)) {
            Storage::disk('public')->delete($report->photo);
        }

        // Hapus semua file dokumen terkait
        $reportFiles = ReportFile::where('report_id', $report_id)->get();
        foreach ($reportFiles as $reportFile) {
            if (Storage::disk('public')->exists($reportFile->name)) {
                Storage::disk('public')->delete($reportFile->name);
            }
            $reportFile->delete();
        }

        // Hapus laporan
        $report->delete();

        return response()->json(['success' => 'Laporan berhasil dihapus'], 200);
    }

    public function updateCommentAndStatus(Request $request, $report_id)
    {

        $validatedData = $request->validate([
            'comment' => 'nullable|string',
            'status' => 'required|in:Diserahkan,Diterima,Dikembalikan',
        ]);


        $report = Report::findOrFail($report_id);


        if ($request->has('comment')) {
            $report->comment = $request->input('comment');
        }


        $report->status = $request->input('status');


        $report->save();

        return response()->json([
            'message' => 'Komentar dan status berhasil diperbarui',
            'data' => $report,
        ], 200);
    }



}





