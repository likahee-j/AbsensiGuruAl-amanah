<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ManualAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date') ?: today()->toDateString();

        $gurus = User::where('role', 'guru')->orderBy('name')->get();
        $existing = Attendance::with('user', 'recorder')
            ->whereDate('date', $date)
            ->get()
            ->keyBy('user_id');

        return view('admin.manual.index', compact('gurus', 'existing', 'date'));
    }

    public function create(Request $request)
    {
        $gurus = User::where('role', 'guru')->orderBy('name')->get();

        return view('admin.manual.create', [
            'gurus' => $gurus,
            'statuses' => Attendance::STATUS_LABELS,
            'defaults' => [
                'date' => $request->input('date', today()->toDateString()),
                'user_id' => $request->input('user_id'),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'date' => ['required', 'date'],
            'status' => ['required', Rule::in(Attendance::STATUSES)],
            'check_in_time' => ['nullable', 'date_format:H:i'],
            'check_out_time' => ['nullable', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $guru = User::findOrFail($data['user_id']);
        abort_unless($guru->role === 'guru', 422, 'User bukan guru.');

        Attendance::updateOrCreate(
            ['user_id' => $data['user_id'], 'date' => $data['date']],
            [
                'status' => $data['status'],
                'check_in_time' => ! empty($data['check_in_time']) ? $data['check_in_time'] . ':00' : null,
                'check_out_time' => ! empty($data['check_out_time']) ? $data['check_out_time'] . ':00' : null,
                'notes' => $data['notes'] ?? null,
                'recorded_by' => $request->user()->id,
            ]
        );

        return redirect()
            ->route('admin.manual.index', ['date' => $data['date']])
            ->with('status', 'Absensi manual berhasil disimpan.');
    }

    public function edit(Attendance $attendance)
    {
        return view('admin.manual.edit', [
            'attendance' => $attendance->load('user'),
            'statuses' => Attendance::STATUS_LABELS,
        ]);
    }

    public function update(Request $request, Attendance $attendance)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(Attendance::STATUSES)],
            'check_in_time' => ['nullable', 'date_format:H:i'],
            'check_out_time' => ['nullable', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $attendance->update([
            'status' => $data['status'],
            'check_in_time' => ! empty($data['check_in_time']) ? $data['check_in_time'] . ':00' : null,
            'check_out_time' => ! empty($data['check_out_time']) ? $data['check_out_time'] . ':00' : null,
            'notes' => $data['notes'] ?? null,
            'recorded_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('admin.manual.index', ['date' => $attendance->date->toDateString()])
            ->with('status', 'Absensi berhasil diperbarui.');
    }

    public function destroy(Attendance $attendance)
    {
        $date = $attendance->date->toDateString();
        $attendance->delete();

        return redirect()
            ->route('admin.manual.index', ['date' => $date])
            ->with('status', 'Catatan absensi dihapus.');
    }

    public function bulkMarkAlpa(Request $request)
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
        ]);

        $userIdsWithRecord = Attendance::whereDate('date', $data['date'])->pluck('user_id')->all();
        $missingGurus = User::where('role', 'guru')->whereNotIn('id', $userIdsWithRecord)->get();

        $count = 0;
        foreach ($missingGurus as $guru) {
            Attendance::create([
                'user_id' => $guru->id,
                'date' => $data['date'],
                'status' => 'alpa',
                'recorded_by' => $request->user()->id,
                'notes' => 'Ditandai alpa otomatis oleh admin.',
            ]);
            $count++;
        }

        return redirect()
            ->route('admin.manual.index', ['date' => $data['date']])
            ->with('status', "Ditandai alpa: {$count} guru.");
    }
}
