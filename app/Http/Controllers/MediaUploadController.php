<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaUploadController extends Controller
{
    public function upload(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'file' => 'required|file',
        ]);

        $file = $request->file('file');
        $maxSize = ((int) Setting::get('media_max_size', 50)) * 1024 * 1024;

        if ($file->getSize() > $maxSize) {
            return response()->json(['error' => 'File exceeds maximum size of ' . Setting::get('media_max_size', '50') . 'MB'], 422);
        }

        $allowedTypes = Setting::get('media_allowed_types', '');
        if (!empty($allowedTypes)) {
            $allowed = array_map('trim', explode(',', $allowedTypes));
            if (!in_array($file->getMimeType(), $allowed)) {
                return response()->json(['error' => 'File type not allowed: ' . $file->getMimeType()], 422);
            }
        }

        $path = $file->store('media', 'public');

        return response()->json([
            'success' => true,
            'path' => $path,
            'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);
    }
}
