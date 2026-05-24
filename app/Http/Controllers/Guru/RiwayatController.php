<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $month = (int) ($request->input('month') ?: now()->month);
        $year = (int) ($request->input('year') ?: now()->year);

        $query = Attendance::where('user_id', $userId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date', 'desc')
            ->orderBy('check_in_time');

        $rows = $query->get();

        $summary = $this->buildSummary($userId, $year, $month);
        $currentMonth = $this->buildSummary($userId, (int) now()->year, (int) now()->month);

        return view('guru.riwayat.index', [
            'rows' => $rows,
            'month' => $month,
            'year' => $year,
            'summary' => $summary,
            'currentMonth' => $currentMonth,
        ]);
    }

    private function buildSummary(int $userId, int $year, int $month): array
    {
        $counts = Attendance::where('user_id', $userId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
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
