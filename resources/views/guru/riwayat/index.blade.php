<x-app-layout>
    <x-slot name="header">Riwayat Absensi</x-slot>

    <div class="card mb-4">
        <div class="card-header section-header">
            <span>Rekapan Bulan Ini ({{ now()->translatedFormat('F Y') }})</span>
        </div>
        <div class="card-body">
            <div class="row row-cols-3 row-cols-sm-6 g-2 text-center">
                @foreach(\App\Models\Attendance::STATUS_LABELS as $key => $label)
                    <div class="col">
                        <div class="border rounded p-2 h-100">
                            <div class="small text-muted">{{ $label }}</div>
                            <div class="fs-5 fw-bold">{{ $currentMonth[$key] ?? 0 }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header section-header">
            <span>Filter</span>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-auto">
                    <label class="form-label small text-muted mb-1">Bulan</label>
                    <select name="month" class="form-select form-select-sm">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" @selected($m == $month)>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label small text-muted mb-1">Tahun</label>
                    <select name="year" class="form-select form-select-sm">
                        @foreach(range(now()->year - 3, now()->year + 1) as $y)
                            <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary btn-sm">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header section-header">
            <span>Rekap {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }} {{ $year }}</span>
        </div>
        <div class="card-body">
            <div class="row row-cols-3 row-cols-sm-6 g-2 text-center">
                @foreach(\App\Models\Attendance::STATUS_LABELS as $key => $label)
                    <div class="col">
                        <div class="border rounded p-2 h-100">
                            <div class="small text-muted">{{ $label }}</div>
                            <div class="fs-5 fw-bold">{{ $summary[$key] ?? 0 }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle datatable" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width:50px">#</th>
                            <th>Tanggal</th>
                            <th>Masuk</th>
                            <th>Pulang</th>
                            <th>Status</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $row)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ \Carbon\Carbon::parse($row->date)->translatedFormat('d M Y') }}</td>
                                <td>{{ $row->check_in_time ?: '-' }}</td>
                                <td>{{ $row->check_out_time ?: '-' }}</td>
                                <td>
                                    <span class="badge rounded-pill
                                        @if($row->status === 'hadir') text-bg-success
                                        @elseif($row->status === 'terlambat') text-bg-warning
                                        @elseif($row->status === 'izin') text-bg-info
                                        @elseif($row->status === 'sakit') text-bg-primary
                                        @else text-bg-danger
                                        @endif">
                                        {{ \App\Models\Attendance::STATUS_LABELS[$row->status] ?? $row->status }}
                                    </span>
                                </td>
                                <td class="small text-muted">{{ $row->notes ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
