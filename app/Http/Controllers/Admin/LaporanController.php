<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $gurus = User::where('role', 'guru')->orderBy('name')->get();

        return view('admin.laporan.index', [
            'gurus' => $gurus,
            'filters' => $this->filters($request),
        ]);
    }

    public function show(Request $request, User $guru)
    {
        abort_unless($guru->role === 'guru', 404);

        $filters = $this->filters($request);
        $rows = $this->query($guru, $filters)->get();

        $summary = $this->summary($guru, $filters);

        return view('admin.laporan.show', [
            'guru' => $guru,
            'rows' => $rows,
            'filters' => $filters,
            'summary' => $summary,
        ]);
    }

    public function print(Request $request, User $guru)
    {
        abort_unless($guru->role === 'guru', 404);

        $filters = $this->filters($request);
        $rows = $this->query($guru, $filters)->get();
        $summary = $this->summary($guru, $filters);

        return view('admin.laporan.print', [
            'guru' => $guru,
            'rows' => $rows,
            'filters' => $filters,
            'summary' => $summary,
        ]);
    }

    public function pdf(Request $request, User $guru)
    {
        abort_unless($guru->role === 'guru', 404);

        $filters = $this->filters($request);
        $rows = $this->query($guru, $filters)->get();
        $summary = $this->summary($guru, $filters);

        $pdf = Pdf::loadView('admin.laporan.pdf', [
            'guru' => $guru,
            'rows' => $rows,
            'filters' => $filters,
            'summary' => $summary,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('laporan-' . str()->slug($guru->name) . '-' . now()->format('Ymd') . '.pdf');
    }

    private function filters(Request $request): array
    {
        return [
            'from' => $request->input('from') ?: Carbon::now()->startOfMonth()->toDateString(),
            'to' => $request->input('to') ?: Carbon::now()->toDateString(),
        ];
    }

    private function query(User $guru, array $filters)
    {
        return Attendance::where('user_id', $guru->id)
            ->whereBetween('date', [$filters['from'], $filters['to']])
            ->orderBy('date');
    }

    private function summary(User $guru, array $filters): array
    {
        $counts = Attendance::where('user_id', $guru->id)
            ->whereBetween('date', [$filters['from'], $filters['to']])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $result = [];
        foreach (Attendance::STATUSES as $s) {
            $result[$s] = (int) ($counts[$s] ?? 0);
        }
        $result['total'] = array_sum($result);
        return $result;
    }
}
