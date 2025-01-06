<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class FileController extends Controller
{
    public function getFile($uuid, $filename)
    {
        $path = storage_path("app/public/reportdocs/{$uuid}/{$filename}");
        
        if (!file_exists($path)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return response()->file($path);
    }

    public function getFoto($uuid, $fotoname)
    {
        $path = storage_path("app/public/reportfoto/{$uuid}/{$fotoname}");
        
        if (!file_exists($path)) {
            return response()->json(['message' => 'Foto not found'], 404);
        }

        return response()->file($path);
    }

}