<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AttendanceExport;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\SchoolSetting;
use App\Models\TahunPelajaran;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class RekapController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->build($request);

        return view('admin.rekap.index', $data);
    }

    public function print(Request $request)
    {
        $data = $this->build($request);
        $data['school'] = SchoolSetting::current();

        return view('admin.rekap.print', $data);
    }

    public function exportExcel(Request $request)
    {
        $data = $this->build($request);

        $rows = Attendance::with('user')
            ->whereBetween('date', [$data['range']['from'], $data['range']['to']])
            ->whereHas('user', fn ($q) => $q->whereIn('role', ['guru', 'kepsek']))
            ->orderBy('date')
            ->get();

        return Excel::download(new AttendanceExport($rows), 'rekap-absensi-'.now()->format('Ymd-His').'.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $data = $this->build($request);
        $data['school'] = SchoolSetting::current();

        $pdf = Pdf::loadView('admin.rekap.print', $data)->setPaper('a4', 'landscape');

        return $pdf->download('rekap-absensi-'.now()->format('Ymd-His').'.pdf');
    }

    /**
     * @return array<string, mixed>
     */
    private function build(Request $request): array
    {
        $tapel = TahunPelajaran::aktif();
        $months = $this->monthOptions($tapel);

        $jenis = $request->input('jenis') === 'semester' ? 'semester' : 'bulan';
        $bulan = $request->input('bulan');
        if (! $bulan || ! array_key_exists($bulan, $months)) {
            $current = Carbon::now()->format('Y-m');
            $bulan = array_key_exists($current, $months) ? $current : array_key_first($months);
        }

        if ($jenis === 'semester' && $tapel) {
            $from = $tapel->mulai->toDateString();
            $to = $tapel->selesai->toDateString();
            $periodLabel = 'Semester '.$tapel->semester.' '.$tapel->tahun;
        } else {
            $start = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
            $from = $start->toDateString();
            $to = $start->copy()->endOfMonth()->toDateString();
            $periodLabel = $start->translatedFormat('F Y');
        }

        $gurus = User::whereIn('role', ['guru', 'kepsek'])->orderBy('name')->get();

        $counts = Attendance::whereIn('user_id', $gurus->pluck('id'))
            ->whereBetween('date', [$from, $to])
            ->get()
            ->groupBy('user_id');

        $rows = [];
        foreach ($gurus as $guru) {
            $byStatus = ($counts[$guru->id] ?? collect())->groupBy('status')->map->count();
            $hadir = (int) ($byStatus['hadir'] ?? 0);
            $terlambat = (int) ($byStatus['terlambat'] ?? 0);
            $izin = (int) ($byStatus['izin'] ?? 0);
            $sakit = (int) ($byStatus['sakit'] ?? 0);
            $alpa = (int) ($byStatus['alpa'] ?? 0) + (int) ($byStatus['tidak_hadir'] ?? 0);
            $rows[] = [
                'guru' => $guru,
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpa' => $alpa,
                'total' => $hadir + $terlambat + $izin + $sakit + $alpa,
            ];
        }

        return [
            'tapel' => $tapel,
            'months' => $months,
            'jenis' => $jenis,
            'bulan' => $bulan,
            'periodLabel' => $periodLabel,
            'range' => ['from' => $from, 'to' => $to],
            'rows' => $rows,
        ];
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
