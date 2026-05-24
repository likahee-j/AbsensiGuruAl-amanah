<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;

class WhatsappController extends Controller
{
    public function compose(Request $request, User $guru)
    {
        abort_unless($guru->role === 'guru', 404);

        $template = $request->input('template');
        $message = $this->buildMessage($guru, $template);

        return view('admin.whatsapp.compose', [
            'guru' => $guru,
            'message' => $message,
            'wa_url' => $this->buildWaUrl($guru->phone, $message),
        ]);
    }

    public function generateLink(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'message' => ['required', 'string', 'max:4096'],
        ]);

        $guru = User::findOrFail($data['user_id']);
        abort_unless($guru->role === 'guru', 404);

        return response()->json([
            'ok' => true,
            'wa_url' => $this->buildWaUrl($guru->phone, $data['message']),
            'phone' => $this->normalizePhone($guru->phone),
        ]);
    }

    private function buildMessage(User $guru, ?string $template): string
    {
        $name = $guru->name;
        $today = today()->translatedFormat('d F Y');

        return match ($template) {
            'belum_absen' => "Assalamu'alaikum Bapak/Ibu {$name},\n\nKami belum menerima absensi masuk Anda untuk hari ini ({$today}). Mohon segera melakukan absensi atau menginformasikan jika berhalangan hadir.\n\nTerima kasih.",
            'terlambat' => "Assalamu'alaikum Bapak/Ibu {$name},\n\nKami mencatat Anda terlambat hari ini ({$today}). Mohon untuk dapat hadir tepat waktu pada hari berikutnya.\n\nTerima kasih.",
            'izin_konfirmasi' => "Assalamu'alaikum Bapak/Ibu {$name},\n\nKami sudah mencatat izin/sakit Anda untuk hari ini ({$today}). Semoga lekas sehat dan bisa kembali bertugas.\n\nTerima kasih.",
            default => "Assalamu'alaikum Bapak/Ibu {$name},\n\n[tulis pesan di sini]",
        };
    }

    private function normalizePhone(?string $phone): string
    {
        $p = preg_replace('/\D+/', '', (string) $phone);
        if ($p === '') {
            return '';
        }
        if (str_starts_with($p, '0')) {
            $p = '62' . substr($p, 1);
        } elseif (! str_starts_with($p, '62')) {
            $p = '62' . $p;
        }
        return $p;
    }

    private function buildWaUrl(?string $phone, string $message): string
    {
        $normalized = $this->normalizePhone($phone);
        $base = $normalized !== ''
            ? "https://wa.me/{$normalized}"
            : 'https://wa.me/';
        return $base . '?text=' . rawurlencode($message);
    }
}
