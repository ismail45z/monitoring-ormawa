<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SecureFileController extends Controller
{
    /**
     * Serve a file securely to authenticated users.
     */
    public function show(Request $request, $path)
    {
        // Ensure the path does not contain path traversal characters for safety
        $path = str_replace(['../', '..\\'], '', $path);
        
        // Ensure the file exists in the local storage (storage/app)
        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'File not found');
        }

        // Return the file securely
        return response()->file(Storage::disk('local')->path($path));
    }
}
