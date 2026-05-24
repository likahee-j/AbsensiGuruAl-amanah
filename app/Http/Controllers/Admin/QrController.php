<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QrToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrController extends Controller
{
    public function index()
    {
        return view('admin.qr.index');
    }

    public function generate(): JsonResponse
    {
        QrToken::where('expires_at', '<', now())->delete();

        $token = (string) Str::uuid();
        $expiresAt = now()->addSeconds(30);

        QrToken::create([
            'token' => $token,
            'expires_at' => $expiresAt,
            'is_used' => false,
        ]);

        $svg = QrCode::format('svg')->size(400)->margin(1)->generate($token);

        return response()->json([
            'token' => $token,
            'expires_at' => $expiresAt->toIso8601String(),
            'ttl_seconds' => 30,
            'qr_svg_base64' => base64_encode($svg),
        ]);
    }
}
