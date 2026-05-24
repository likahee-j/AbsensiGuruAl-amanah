<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->filled('date')
            ? Carbon::parse($request->input('date'))->toDateString()
            : today()->toDateString();

        $gurus = User::whereIn('role', ['guru', 'kepsek'])->orderBy('name')->get();
        $attendances = Attendance::with('user')
            ->whereDate('date', $date)
            ->whereHas('user', fn ($q) => $q->whereIn('role', ['guru', 'kepsek']))
            ->orderBy('check_in_time')
            ->get();

        $byStatus = $attendances->groupBy('status')->map->count();
        $stats = [
            'total_guru' => $gurus->count(),
            'hadir' => (int) ($byStatus['hadir'] ?? 0),
            'terlambat' => (int) ($byStatus['terlambat'] ?? 0),
            'izin' => (int) ($byStatus['izin'] ?? 0),
            'sakit' => (int) ($byStatus['sakit'] ?? 0),
            'alpa' => (int) ($byStatus['alpa'] ?? 0),
        ];
        $stats['belum_absen'] = max(0, $stats['total_guru'] - $attendances->count());

        $absentGurus = $gurus->reject(fn ($g) => $attendances->contains('user_id', $g->id))->values();

        return view('admin.dashboard', [
            'date' => $date,
            'stats' => $stats,
            'attendances' => $attendances,
            'absentGurus' => $absentGurus,
        ]);
    }
}
