<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ShortenedUrl;
use Illuminate\Support\Str;

class ShortenUrlController extends Controller
{
    public function shortenUrl(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $originalUrl = $request->url;

        $existingUrl = ShortenedUrl::where('user_id', $request->user()->id)
            ->where('original_url', $originalUrl)
            ->first();

        if ($existingUrl) {
            return response()->json([
                'status' => 'success',
                'message' => 'URL already shortened',
                'data' => [
                    'original_url' => $existingUrl->original_url,
                    'short_code' => $existingUrl->short_code,
                    'short_url' => url($existingUrl->short_code),
                ]
            ], 200);
        }

        do {
            $shortCode = Str::random(6);
            $exists = ShortenedUrl::where('short_code', $shortCode)->exists();
        } while ($exists);

        $shortenedUrl = ShortenedUrl::create([
            'user_id' => $request->user()->id,
            'original_url' => $originalUrl,
            'short_code' => $shortCode,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'URL shortened successfully',
            'data' => [
                'original_url' => $shortenedUrl->original_url,
                'short_code' => $shortenedUrl->short_code,
                'short_url' => url($shortenedUrl->short_code),
            ]
        ], 201);
    }

    // List all shortened URLs for the authenticated user
    public function index(Request $request)
    {
        $urls = ShortenedUrl::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'original_url', 'short_code', 'created_at']);

        return response()->json([
            'status' => 'success',
            'data' => $urls->map(function ($url) {
                return [
                    'id' => $url->id,
                    'original_url' => $url->original_url,
                    'short_code' => $url->short_code,
                    'short_url' => url($url->short_code),
                    'created_at' => $url->created_at->toISOString(),
                ];
            })
        ], 200);
    }
}
