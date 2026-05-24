<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\SchoolSetting;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class KioskController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.kiosk.index', [
            'settings' => SchoolSetting::current(),
            'operator' => $request->user(),
        ]);
    }

    /**
     * Scan QR identitas guru/kepsek dari laptop kios.
     * Identitas diambil dari payload QR, bukan dari user yang login.
     * Mode (check-in / check-out) ditentukan otomatis berdasarkan
     * kondisi attendance hari ini.
     */
    public function scan(Request $request): JsonResponse
    {
        $data = $request->validate([
            'payload' => ['required', 'string', 'max:255'],
        ]);

        $user = $this->resolveUserFromPayload($data['payload']);

        if (! $user) {
            return response()->json([
                'ok' => false,
                'message' => 'QR tidak dikenali. Pastikan kartu QR milik guru/kepsek terdaftar.',
            ], 422);
        }

        if (! in_array($user->role, ['guru', 'kepsek'], true)) {
            return response()->json([
                'ok' => false,
                'message' => 'QR bukan milik guru/kepsek.',
            ], 422);
        }

        $settings = SchoolSetting::current();
        $now = Carbon::now();
        $today = today()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        $hasCheckIn = $attendance && $attendance->check_in_time;
        $hasCheckOut = $attendance && $attendance->check_out_time;

        if ($hasCheckIn && $hasCheckOut) {
            return response()->json([
                'ok' => false,
                'message' => $user->name.' sudah absen masuk & pulang hari ini.',
                'user' => $this->userPayload($user),
                'attendance' => $this->attendancePayload($attendance),
            ], 422);
        }

        $mode = ! $hasCheckIn ? 'checkin' : 'checkout';

        if ($mode === 'checkin') {
            $start = Carbon::parse($settings->check_in_start);
            $end = Carbon::parse($settings->check_in_end);
            $lateThreshold = Carbon::parse($settings->late_threshold);
            $nowTime = $now->copy()->setDate($start->year, $start->month, $start->day);

            if ($nowTime->lt($start) || $nowTime->gt($end)) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Waktu absen masuk: '.substr($settings->check_in_start, 0, 5).' - '.substr($settings->check_in_end, 0, 5).'.',
                    'user' => $this->userPayload($user),
                ], 422);
            }

            $status = $nowTime->gt($lateThreshold) ? 'terlambat' : 'hadir';

            $attendance = $attendance ?? new Attendance([
                'user_id' => $user->id,
                'date' => $today,
            ]);

            $attendance->fill([
                'check_in_time' => $now->format('H:i:s'),
                'status' => $status,
                'latitude' => $settings->latitude,
                'longitude' => $settings->longitude,
                'qr_token_used' => 'KIOSK',
                'recorded_by' => $request->user()->id,
            ])->save();

            return response()->json([
                'ok' => true,
                'mode' => 'checkin',
                'message' => 'Absen masuk berhasil. Status: '.strtoupper($status).'.',
                'user' => $this->userPayload($user),
                'attendance' => $this->attendancePayload($attendance),
            ]);
        }

        // checkout
        if ($settings->check_out_start && $settings->check_out_end) {
            $start = Carbon::parse($settings->check_out_start);
            $end = Carbon::parse($settings->check_out_end);
            $nowTime = $now->copy()->setDate($start->year, $start->month, $start->day);

            if ($nowTime->lt($start) || $nowTime->gt($end)) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Waktu absen pulang: '.substr($settings->check_out_start, 0, 5).' - '.substr($settings->check_out_end, 0, 5).'.',
                    'user' => $this->userPayload($user),
                ], 422);
            }
        }

        $attendance->fill([
            'check_out_time' => $now->format('H:i:s'),
            'out_latitude' => $settings->latitude,
            'out_longitude' => $settings->longitude,
            'qr_token_used_out' => 'KIOSK',
        ])->save();

        return response()->json([
            'ok' => true,
            'mode' => 'checkout',
            'message' => 'Absen pulang berhasil dicatat.',
            'user' => $this->userPayload($user),
            'attendance' => $this->attendancePayload($attendance),
        ]);
    }

    /**
     * QR identitas user berisi `username` (lihat PenggunaController@qr),
     * atau fallback `USER-{id}`. Kita dukung keduanya.
     */
    private function resolveUserFromPayload(string $payload): ?User
    {
        $payload = trim($payload);

        if (preg_match('/^USER-(\d+)$/', $payload, $m)) {
            return User::find((int) $m[1]);
        }

        return User::where('username', $payload)->first();
    }

    /**
     * @return array<string, mixed>
     */
    private function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'role_label' => $user->roleLabel(),
            'photo_url' => $user->photo ? asset('storage/'.$user->photo) : null,
            'nip' => $user->nip,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function attendancePayload(Attendance $attendance): array
    {
        return [
            'date' => (string) $attendance->date,
            'check_in_time' => $attendance->check_in_time,
            'check_out_time' => $attendance->check_out_time,
            'status' => $attendance->status,
            'status_label' => Attendance::STATUS_LABELS[$attendance->status] ?? $attendance->status,
        ];
    }
}
