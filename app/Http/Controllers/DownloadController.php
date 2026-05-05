<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadController extends Controller
{
    public function index()
    {
        return view('download');
    }

    public function downloadApk(): BinaryFileResponse
    {
        $filePath = storage_path('app/public/downloads/SatuKasir-POS-v1.1.apk');
        
        if (!file_exists($filePath)) {
            abort(404, 'APK file not found');
        }

        return response()->download($filePath, 'SatuKasir-POS-v1.1.apk', [
            'Content-Type' => 'application/vnd.android.package-archive',
        ]);
    }
}