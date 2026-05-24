<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\QrToken;
use App\Models\SchoolSetting;
use App\Support\Haversine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $today = Attendance::where('user_id', $request->user()->id)
            ->whereDate('date', today())
            ->first();

        return view('guru.absensi.index', [
            'todayAttendance' => $today,
            'settings' => SchoolSetting::current(),
        ]);
    }

    public function scan(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'mode' => ['nullable', 'in:checkin,checkout'],
        ]);

        $mode = $data['mode'] ?? 'checkin';

        $token = QrToken::where('token', $data['token'])->first();

        if (! $token) {
            return response()->json(['ok' => false, 'message' => 'QR tidak dikenali.'], 422);
        }

        if ($token->isExpired()) {
            return response()->json(['ok' => false, 'message' => 'QR sudah kedaluwarsa.'], 422);
        }

        $existing = Attendance::where('user_id', $request->user()->id)
            ->whereDate('date', today())
            ->first();

        if ($mode === 'checkin' && $existing && $existing->check_in_time) {
            return response()->json([
                'ok' => false,
                'message' => 'Anda sudah melakukan absen masuk hari ini pada pukul ' . $existing->check_in_time . '.',
            ], 422);
        }

        if ($mode === 'checkout') {
            if (! $existing || ! $existing->check_in_time) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Anda belum melakukan absen masuk hari ini.',
                ], 422);
            }
            if ($existing->check_out_time) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Anda sudah melakukan absen pulang pada pukul ' . $existing->check_out_time . '.',
                ], 422);
            }
        }

        return response()->json([
            'ok' => true,
            'message' => 'Token valid. Mohon izinkan akses lokasi.',
            'token' => $token->token,
            'mode' => $mode,
        ]);
    }

    public function checkin(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $user = $request->user();
        $settings = SchoolSetting::current();

        $existing = Attendance::where('user_id', $user->id)
            ->whereDate('date', today())
            ->first();

        if ($existing && $existing->check_in_time) {
            return response()->json([
                'ok' => false,
                'message' => 'Anda sudah absen masuk hari ini.',
            ], 422);
        }

        $token = QrToken::where('token', $data['token'])->first();
        if (! $token || $token->isExpired()) {
            return response()->json([
                'ok' => false,
                'message' => 'Token tidak valid / kedaluwarsa.',
            ], 422);
        }

        $distance = Haversine::distanceMeters(
            (float) $data['latitude'],
            (float) $data['longitude'],
            (float) $settings->latitude,
            (float) $settings->longitude
        );

        if ($distance > $settings->radius_meters) {
            return response()->json([
                'ok' => false,
                'message' => 'Anda berada di luar radius sekolah (jarak ' . number_format($distance, 0) . ' m). Radius maks: ' . $settings->radius_meters . ' m.',
            ], 422);
        }

        $now = Carbon::now();
        $start = Carbon::parse($settings->check_in_start);
        $end = Carbon::parse($settings->check_in_end);
        $lateThreshold = Carbon::parse($settings->late_threshold);
        $nowTime = $now->copy()->setDate($start->year, $start->month, $start->day);

        if ($nowTime->lt($start) || $nowTime->gt($end)) {
            return response()->json([
                'ok' => false,
                'message' => 'Waktu absen masuk: ' . substr($settings->check_in_start, 0, 5) . ' - ' . substr($settings->check_in_end, 0, 5) . '.',
            ], 422);
        }

        $status = $nowTime->gt($lateThreshold) ? 'terlambat' : 'hadir';

        $attendance = $existing ?? new Attendance([
            'user_id' => $user->id,
            'date' => today()->toDateString(),
        ]);

        $attendance->fill([
            'check_in_time' => $now->format('H:i:s'),
            'status' => $status,
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'qr_token_used' => $token->token,
        ])->save();

        return response()->json([
            'ok' => true,
            'message' => $status === 'hadir' ? 'Absen masuk berhasil. Status: HADIR.' : 'Absen masuk berhasil. Status: TERLAMBAT.',
            'data' => [
                'name' => $user->name,
                'check_in_time' => $attendance->check_in_time,
                'status' => $attendance->status,
                'distance_meters' => round($distance, 1),
            ],
        ]);
    }

    public function checkout(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $user = $request->user();
        $settings = SchoolSetting::current();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', today())
            ->first();

        if (! $attendance || ! $attendance->check_in_time) {
            return response()->json([
                'ok' => false,
                'message' => 'Anda belum absen masuk hari ini.',
            ], 422);
        }

        if ($attendance->check_out_time) {
            return response()->json([
                'ok' => false,
                'message' => 'Anda sudah absen pulang pada ' . $attendance->check_out_time . '.',
            ], 422);
        }

        $token = QrToken::where('token', $data['token'])->first();
        if (! $token || $token->isExpired()) {
            return response()->json([
                'ok' => false,
                'message' => 'Token tidak valid / kedaluwarsa.',
            ], 422);
        }

        $distance = Haversine::distanceMeters(
            (float) $data['latitude'],
            (float) $data['longitude'],
            (float) $settings->latitude,
            (float) $settings->longitude
        );

        if ($distance > $settings->radius_meters) {
            return response()->json([
                'ok' => false,
                'message' => 'Anda berada di luar radius sekolah (jarak ' . number_format($distance, 0) . ' m).',
            ], 422);
        }

        $now = Carbon::now();

        if ($settings->check_out_start && $settings->check_out_end) {
            $start = Carbon::parse($settings->check_out_start);
            $end = Carbon::parse($settings->check_out_end);
            $nowTime = $now->copy()->setDate($start->year, $start->month, $start->day);

            if ($nowTime->lt($start) || $nowTime->gt($end)) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Waktu absen pulang: ' . substr($settings->check_out_start, 0, 5) . ' - ' . substr($settings->check_out_end, 0, 5) . '.',
                ], 422);
            }
        }

        $attendance->update([
            'check_out_time' => $now->format('H:i:s'),
            'out_latitude' => $data['latitude'],
            'out_longitude' => $data['longitude'],
            'qr_token_used_out' => $token->token,
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Absen pulang berhasil dicatat.',
            'data' => [
                'name' => $user->name,
                'check_in_time' => $attendance->check_in_time,
                'check_out_time' => $attendance->check_out_time,
                'status' => $attendance->status,
            ],
        ]);
    }

    /**
     * Simulasi absensi tanpa kamera & GPS (untuk uji coba / demo).
     */
    public function simulate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'mode' => ['required', 'in:checkin,checkout'],
        ]);

        $user = $request->user();
        $settings = SchoolSetting::current();
        $now = Carbon::now();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', today())
            ->first();

        if ($data['mode'] === 'checkin') {
            if ($attendance && $attendance->check_in_time) {
                return response()->json(['ok' => false, 'message' => 'Anda sudah absen masuk hari ini.'], 422);
            }

            $status = $now->format('H:i:s') > (string) $settings->late_threshold ? 'terlambat' : 'hadir';

            $attendance = $attendance ?? new Attendance([
                'user_id' => $user->id,
                'date' => today()->toDateString(),
            ]);

            $attendance->fill([
                'check_in_time' => $now->format('H:i:s'),
                'status' => $status,
                'latitude' => $settings->latitude,
                'longitude' => $settings->longitude,
                'qr_token_used' => 'SIMULASI',
            ])->save();

            return response()->json([
                'ok' => true,
                'message' => 'Simulasi absen masuk berhasil. Status: '.strtoupper($status).'.',
                'data' => [
                    'name' => $user->name,
                    'check_in_time' => $attendance->check_in_time,
                    'status' => $attendance->status,
                ],
            ]);
        }

        if (! $attendance || ! $attendance->check_in_time) {
            return response()->json(['ok' => false, 'message' => 'Anda belum absen masuk hari ini.'], 422);
        }

        if ($attendance->check_out_time) {
            return response()->json(['ok' => false, 'message' => 'Anda sudah absen pulang hari ini.'], 422);
        }

        $attendance->fill([
            'check_out_time' => $now->format('H:i:s'),
            'out_latitude' => $settings->latitude,
            'out_longitude' => $settings->longitude,
            'qr_token_used_out' => 'SIMULASI',
        ])->save();

        return response()->json([
            'ok' => true,
            'message' => 'Simulasi absen pulang berhasil.',
            'data' => [
                'name' => $user->name,
                'check_in_time' => $attendance->check_in_time,
                'check_out_time' => $attendance->check_out_time,
                'status' => $attendance->status,
            ],
        ]);
    }
}
