<x-app-layout>
    <x-slot name="header">Laporan: {{ $guru->name }}</x-slot>

    {{-- Filter & Export --}}
    <div class="card mb-4">
        <div class="card-header section-header">
            <span>Filter Periode</span>
            <div class="d-flex gap-1">
                <a href="{{ route('admin.laporan.print', array_merge(['guru' => $guru], $filters)) }}" target="_blank" rel="noopener" class="btn btn-secondary btn-action" title="Cetak">
                    <i class="bi bi-printer"></i>
                </a>
                <a href="{{ route('admin.laporan.pdf', array_merge(['guru' => $guru], $filters)) }}" class="btn btn-danger btn-action" title="Export PDF">
                    <i class="bi bi-file-earmark-pdf"></i>
                </a>
                <a href="{{ route('admin.wa.compose', $guru) }}" class="btn btn-success btn-action" title="Kirim WA">
                    <i class="bi bi-whatsapp"></i>
                </a>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-auto">
                    <label class="form-label small text-muted mb-1">Dari</label>
                    <input type="date" name="from" value="{{ $filters['from'] }}" class="form-control form-control-sm">
                </div>
                <div class="col-auto">
                    <label class="form-label small text-muted mb-1">Sampai</label>
                    <input type="date" name="to" value="{{ $filters['to'] }}" class="form-control form-control-sm">
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary btn-sm">Filter</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Summary Stat Cards --}}
    <div class="row g-3 mb-4">
        @foreach(\App\Models\Attendance::STATUS_LABELS as $key => $label)
            <div class="col-6 col-sm-4 col-lg-2">
                <div class="card h-100 text-center">
                    <div class="card-body p-3">
                        <div class="small text-muted">{{ $label }}</div>
                        <div class="fs-4 fw-bold
                            @if($key === 'hadir') text-success
                            @elseif($key === 'terlambat') text-warning
                            @elseif($key === 'izin') text-info
                            @elseif($key === 'sakit') text-primary
                            @elseif($key === 'alpa') text-danger
                            @endif">{{ $summary[$key] ?? 0 }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Attendance DataTable --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle datatable" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width:50px">#</th>
                            <th>Tanggal</th>
                            <th>Jam Masuk</th>
                            <th>Jam Pulang</th>
                            <th>Status</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $r)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ \Carbon\Carbon::parse($r->date)->format('Y-m-d') }}</td>
                                <td>{{ $r->check_in_time ?: '-' }}</td>
                                <td>{{ $r->check_out_time ?: '-' }}</td>
                                <td>
                                    <span class="badge rounded-pill
                                        @if($r->status === 'hadir') text-bg-success
                                        @elseif($r->status === 'terlambat') text-bg-warning
                                        @elseif($r->status === 'izin') text-bg-info
                                        @elseif($r->status === 'sakit') text-bg-primary
                                        @else text-bg-danger
                                        @endif">
                                        {{ \App\Models\Attendance::STATUS_LABELS[$r->status] ?? $r->status }}
                                    </span>
                                </td>
                                <td class="small text-muted">{{ $r->notes ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
