<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>

    {{-- Filter --}}
    <form method="GET" class="row g-2 align-items-end mb-4" style="max-width:480px">
        <div class="col-auto">
            <label class="form-label small text-muted mb-1">Tanggal</label>
            <input type="date" name="date" value="{{ $date }}" class="form-control form-control-sm">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-light border btn-sm">Reset</a>
        </div>
    </form>

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4">
        @php($cards = [
            ['Total Guru', $stats['total_guru'], 'bi-people-fill', 'text-secondary'],
            ['Hadir', $stats['hadir'], 'bi-check-circle-fill', 'text-success'],
            ['Terlambat', $stats['terlambat'], 'bi-clock-fill', 'text-warning'],
            ['Izin', $stats['izin'], 'bi-calendar-check-fill', 'text-info'],
            ['Sakit', $stats['sakit'], 'bi-bandaid-fill', 'text-primary'],
            ['Alpa', $stats['alpa'], 'bi-x-circle-fill', 'text-danger'],
            ['Belum Absen', $stats['belum_absen'], 'bi-dash-circle-fill', 'text-secondary'],
        ])
        @foreach($cards as [$label, $value, $icon, $color])
            <div class="col-6 col-sm-4 col-lg">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <i class="bi {{ $icon }} fs-3 {{ $color }}"></i>
                        <div class="small text-muted mt-1">{{ $label }}</div>
                        <div class="fs-3 fw-bold {{ $color }}">{{ $value }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header section-header">
                    <span>Absensi {{ \Carbon\Carbon::parse($date)->translatedFormat('d M Y') }}</span>
                    <span class="badge bg-light text-dark">{{ $attendances->count() }} entri</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-dark-head" style="width:100%">
                            <thead>
                                <tr>
                                    <th style="width:50px">#</th>
                                    <th>Nama</th>
                                    <th>Masuk</th>
                                    <th>Pulang</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $a)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="fw-medium">{{ $a->user->name ?? '-' }}</td>
                                        <td>{{ $a->check_in_time ?: '-' }}</td>
                                        <td>{{ $a->check_out_time ?: '-' }}</td>
                                        <td>
                                            <span class="badge rounded-pill
                                                @if($a->status === 'hadir') text-bg-success
                                                @elseif($a->status === 'terlambat') text-bg-warning
                                                @elseif($a->status === 'izin') text-bg-info
                                                @elseif($a->status === 'sakit') text-bg-primary
                                                @else text-bg-danger @endif">
                                                {{ \App\Models\Attendance::STATUS_LABELS[$a->status] ?? $a->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-muted py-3 text-center">Belum ada absensi pada tanggal ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header section-header">
                    <span>Belum Absen</span>
                    <a href="{{ route('admin.absensi.index') }}" class="btn btn-light text-dark">Kelola</a>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($absentGurus as $g)
                            <li class="list-group-item">
                                <div class="fw-medium small">{{ $g->name }}</div>
                                <div class="small text-muted">{{ $g->phone ?: 'Tidak ada telepon' }}</div>
                            </li>
                        @endforeach
                    </ul>
                    @if($absentGurus->isEmpty())
                        <div class="p-4 text-center text-muted small">Semua guru sudah tercatat.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
