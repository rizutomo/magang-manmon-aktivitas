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
       $reports = Report::findOrFail($task_id);
    }


    public function submitReport(Request $request)
    {
        $user_id = Auth::guard('')->user()->id;
        $task_id = $request->input('task_id');
        $user = User::findOrFail($user_id);
        $task = Task::findOrFail($task_id);
    
        $request->validate([
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'description' => 'required|string',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'photo' => 'required|mimes:jpg,png|max:61440',
            'document' => 'nullable|mimes:pdf,doc,docx|max:20480', // Validasi file dokumen
        ], [
            'photo.required' => 'Harap tambahkan foto laporan anda',
            'photo.mimes' => 'Tipe file foto tidak valid',
            'photo.max' => 'Ukuran foto terlalu besar. Max : 60 MB',
            'document.mimes' => 'Tipe file dokumen tidak valid',
            'document.max' => 'Ukuran dokumen terlalu besar. Max : 20 MB',
        ]);
    
        $existingReport = $user->tasks()->where('task_id', $task_id)->first();
    
        if ($existingReport) {
            if ($existingReport->pivot->file) {
                if (Storage::disk('local')->exists($existingReport->pivot->file)) {
                    Storage::disk('local')->delete($existingReport->pivot->file);
                }
            }
    
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $originalName = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $photo->getClientOriginalExtension();
                $randomString = Str::random(15);
                $newPhotoName = $originalName . '_' . $randomString . '.' . $extension;
                $photoPath = Storage::disk('local')->putFileAs('reportfoto/' . $task->id, $photo, $newPhotoName);
            }
    
            // Update data pada tabel `reports`
            $user->tasks()->updateExistingPivot($task_id, [
                'photo' => $photoPath,
                'date' => $request->input('date'),
                'description' => $request->input('description'),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
            ]);
            $report = Report::where('user_id', $user_id)->where('task_id', $task_id)->first();
    
            // Menyimpan dokumen report di tabel `report_file`
            if ($report) {
                $reportId = $report->id;
            if ($request->hasFile('document')) {
                $document = $request->file('document');
                $documentName = pathinfo($document->getClientOriginalName(), PATHINFO_FILENAME);
                $documentExtension = $document->getClientOriginalExtension();
                $documentRandomString = Str::random(15);
                $newDocumentName = $documentName . '_' . $documentRandomString . '.' . $documentExtension;
                $documentPath = Storage::disk('local')->putFileAs('reportdocs/' . $task->id, $document, $newDocumentName);
    
                // Buat entri baru di tabel `report_file`
                ReportFile::create([
                    'report_id' => $reportId,
                    'name' => $documentPath,
                ]);
            }
    
            return response()->json(['success' => 'Pengumpulan berhasil diperbarui'], 200);
        }
    }
        
        return response()->json(['error' => 'Pengumpulan gagal'], 404);
    }
    

    

   
}