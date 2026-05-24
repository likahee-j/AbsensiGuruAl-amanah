<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\HariLibur;
use App\Models\TahunPelajaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class KelolaAbsensiController extends Controller
{
    public function index(Request $request)
    {
        $tapel = TahunPelajaran::aktif();

        $months = $this->monthOptions($tapel);
        $selected = $request->input('bulan');
        if (! $selected || ! array_key_exists($selected, $months)) {
            $current = Carbon::now()->format('Y-m');
            $selected = array_key_exists($current, $months) ? $current : array_key_first($months);
        }

        $start = Carbon::createFromFormat('Y-m', $selected)->startOfMonth();
        $end = (clone $start)->endOfMonth();

        $holidays = HariLibur::whereBetween('tanggal', [$start, $end])
            ->get()
            ->keyBy(fn ($h) => $h->tanggal->toDateString());

        $days = [];
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $key = $d->toDateString();
            $isWeekend = $d->isWeekend();
            $holiday = $holidays->get($key);
            $days[] = [
                'date' => $key,
                'num' => $d->day,
                'dow' => $d->isoWeekday(),
                'is_weekend' => $isWeekend,
                'is_holiday' => $isWeekend || $holiday !== null,
                'holiday_label' => $holiday?->keterangan ?? ($isWeekend ? 'Akhir Pekan' : null),
            ];
        }

        $gurus = User::whereIn('role', ['guru', 'kepsek'])->orderBy('name')->get();

        $records = Attendance::whereIn('user_id', $gurus->pluck('id'))
            ->whereBetween('date', [$start, $end])
            ->get()
            ->groupBy('user_id');

        $rows = [];
        foreach ($gurus as $guru) {
            $byDate = ($records[$guru->id] ?? collect())->keyBy(fn ($a) => Carbon::parse($a->date)->toDateString());
            $summary = ['S' => 0, 'I' => 0, 'A' => 0];
            foreach ($byDate as $att) {
                if ($att->status === 'sakit') {
                    $summary['S']++;
                } elseif ($att->status === 'izin') {
                    $summary['I']++;
                } elseif (in_array($att->status, ['alpa', 'tidak_hadir'], true)) {
                    $summary['A']++;
                }
            }
            $rows[] = [
                'guru' => $guru,
                'records' => $byDate,
                'summary' => $summary,
            ];
        }

        return view('admin.absensi.index', [
            'tapel' => $tapel,
            'months' => $months,
            'selected' => $selected,
            'monthLabel' => $start->translatedFormat('F Y'),
            'days' => $days,
            'rows' => $rows,
            'statuses' => Attendance::STATUS_LABELS,
        ]);
    }

    public function updateCell(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'date' => ['required', 'date'],
            'status' => ['required', Rule::in(Attendance::STATUSES)],
            'check_in_time' => ['nullable', 'date_format:H:i'],
            'check_out_time' => ['nullable', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $attendance = Attendance::where('user_id', $data['user_id'])
            ->whereDate('date', $data['date'])
            ->first() ?? new Attendance([
                'user_id' => $data['user_id'],
                'date' => $data['date'],
            ]);

        $attendance->fill([
            'status' => $data['status'],
            'check_in_time' => $data['check_in_time'] ? $data['check_in_time'].':00' : null,
            'check_out_time' => $data['check_out_time'] ? $data['check_out_time'].':00' : null,
            'notes' => $data['notes'] ?? null,
            'recorded_by' => $request->user()->id,
        ])->save();

        return back()->with('status', 'Data absensi berhasil disimpan.');
    }

    /**
     * @return array<string, string>
     */
    private function monthOptions(?TahunPelajaran $tapel): array
    {
        $months = [];

        if ($tapel) {
            $cursor = $tapel->mulai->copy()->startOfMonth();
            $last = $tapel->selesai->copy()->startOfMonth();
            while ($cursor->lte($last)) {
                $months[$cursor->format('Y-m')] = $cursor->translatedFormat('F Y');
                $cursor->addMonth();
            }
        }

        if (empty($months)) {
            $now = Carbon::now();
            $months[$now->format('Y-m')] = $now->translatedFormat('F Y');
        }

        return $months;
    }
}
