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

        // Ambil laporan yang berelasi dengan task
        $report = $task->report; // Pastikan relasi ini sudah didefinisikan di model Task


        return response([
            'report' => $report,
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

    public function update(Request $request, $report_id)
{
    // Cari laporan berdasarkan report_id dari parameter URL
    $report = Report::findOrFail($report_id);
    $user_id = auth()->user()->id;

    // Validasi input
    $validatedData = $request->validate([
        'date' => 'nullable|date',
        'description' => 'nullable|string',
        'latitude' => 'nullable|string',
        'longitude' => 'nullable|string',
        'photo' => 'nullable|mimes:jpg,png|max:61440',
        'documents.*' => 'nullable|mimes:pdf,doc,docx|max:20480',
    ]);

    // Perbarui data hanya jika input tersedia
    $report->date = $request->input('date', $report->date);
    $report->description = $request->input('description', $report->description);
    $report->latitude = $request->input('latitude', $report->latitude);
    $report->longitude = $request->input('longitude', $report->longitude);

    // Assign ID user sebagai modifier
    $report->modified_by = $user_id;

    // Proses upload foto jika ada
    if ($request->hasFile('photo')) {
        if ($report->photo) {
            Storage::disk('public')->delete($report->photo);
        }
        $report->photo = $request->file('photo')->store('reportfoto/' . $report->task_id, 'public');
    }

    // Simpan perubahan pada model
    $report->save();

    // Proses dokumen jika ada
    if ($request->hasFile('documents')) {
        foreach ($request->file('documents') as $document) {
            $documentPath = $document->store('reportdocs/' . $report->task_id, 'public');
            ReportFile::create([
                'report_id' => $report->id,
                'name' => $documentPath,
            ]);
        }
    }

    // Ambil data laporan beserta file terkait untuk respon
    $report = Report::with('files')->find($report->id);

    return response()->json([
        'message' => 'Laporan berhasil diperbarui',
        'data' => $report,
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

        // dd($existingReport);

        if (!$existingReport) {
            // Menghapus foto lama jika ada
            // if ($existingReport->photo && Storage::disk('public')->exists($existingReport->photo)) {
            //     Storage::disk('public')->delete($existingReport->photo);
            // }

            // Simpan foto baru jika ada
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $photoPath = $photo->store('reportfoto/' . $task->id, 'public');
            } else {
                $photoPath = null;
                
            }

            // Update data pada tabel `reports`
            $existingReport=new Report();
            $existingReport->task_id = $request->input('task_id');
            $existingReport->date = $request->input('date');
            $existingReport->description = $request->input('description');
            $existingReport->latitude = $request->input('latitude');
            $existingReport->longitude = $request->input('longitude');
            $existingReport->photo = $photoPath;
            $existingReport->modified_by = $user_id;
            $existingReport->save();
            
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
        'status' => 'required|in:Diserahkan,Diterima,Pending',
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





