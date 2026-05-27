<?php

namespace App\Http\Controllers\Backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleDriveProxyController extends Controller
{
    /**
     * Proxy request to preview images from Google Drive helper container.
     */
    public function preview(Request $request)
    {
        $path = $request->get('path');

        if (empty($path)) {
            return response()->json(['error' => 'Path parameter is required'], 400);
        }

        // Resolving full path for Google Drive
        $fullPath = $path;
        if (!str_starts_with($path, 'MKAS LARAVEL STORAGE')) {
            // For transaction paths like receipts/xxxx.png or simple filename,
            // prefix it with the standard transaction folder path.
            $fullPath = 'MKAS LARAVEL STORAGE/TRANSACTIONS/' . ltrim($path, '/');
        }

        Log::info("Proxying preview request to gdrive-service for path: {$fullPath}");

        try {
            $baseUrl = env('GDRIVE_SERVICE_URL', 'http://gdrive-service:8000');
            $response = Http::get("{$baseUrl}/api/preview", [
                'path' => $fullPath
            ]);

            if (!$response->successful()) {
                Log::error("Failed to fetch image from gdrive-service for path '{$fullPath}'. Status: {$response->status()} - Response: " . $response->body());
                return response()->json(['error' => 'Failed to retrieve file from Google Drive service'], $response->status());
            }

            $contentType = $response->header('Content-Type') ?: 'application/octet-stream';
            $contentLength = $response->header('Content-Length');
            $contentDisposition = $response->header('Content-Disposition') ?: 'inline';
            $cacheControl = $response->header('Cache-Control') ?: 'private, max-age=86400';

            $laravelResponse = response($response->body(), 200)
                ->header('Content-Type', $contentType)
                ->header('Content-Disposition', $contentDisposition)
                ->header('Cache-Control', $cacheControl);

            if ($contentLength) {
                $laravelResponse->header('Content-Length', $contentLength);
            }

            return $laravelResponse;

        } catch (\Exception $e) {
            Log::error("Exception in GoogleDriveProxyController preview: " . $e->getMessage());
            return response()->json(['error' => 'Google Drive proxy error: ' . $e->getMessage()], 500);
        }
    }
}
