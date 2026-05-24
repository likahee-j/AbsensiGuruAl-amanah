<x-app-layout>
    <x-slot name="header">Input Absensi Manual</x-slot>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-auto">
                    <label class="form-label small text-muted mb-1">Tanggal</label>
                    <input type="date" name="date" value="{{ $date }}" class="form-control form-control-sm">
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary btn-sm">Tampilkan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="d-flex gap-2 mb-3">
        <a href="{{ route('admin.manual.create', ['date' => $date]) }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Tambah Absensi
        </a>

        <form method="POST" action="{{ route('admin.manual.bulk_alpa') }}" onsubmit="return confirm('Tandai semua guru yang belum tercatat sebagai ALPA pada tanggal ini?');">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">
            <button class="btn btn-danger">Tandai Sisa = Alpa</button>
        </form>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle datatable" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width:50px">#</th>
                            <th>Guru</th>
                            <th>Status</th>
                            <th>Jam Masuk</th>
                            <th>Jam Pulang</th>
                            <th>Catatan</th>
                            <th>Dicatat oleh</th>
                            <th class="text-center" style="width:150px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($gurus as $g)
                            @php $a = $existing[$g->id] ?? null; @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-medium">{{ $g->name }}</td>
                                <td>
                                    @if($a)
                                        <span class="badge rounded-pill
                                            @if($a->status === 'hadir') text-bg-success
                                            @elseif($a->status === 'terlambat') text-bg-warning
                                            @elseif($a->status === 'izin') text-bg-info
                                            @elseif($a->status === 'sakit') text-bg-primary
                                            @else text-bg-danger
                                            @endif">
                                            {{ \App\Models\Attendance::STATUS_LABELS[$a->status] ?? $a->status }}
                                        </span>
                                    @else
                                        <span class="text-muted small fst-italic">Belum ada</span>
                                    @endif
                                </td>
                                <td>{{ $a->check_in_time ?? '-' }}</td>
                                <td>{{ $a->check_out_time ?? '-' }}</td>
                                <td class="small text-muted">{{ $a->notes ?? '-' }}</td>
                                <td class="small text-muted">{{ $a?->recorder?->name ?? ($a ? '— scan QR —' : '-') }}</td>
                                <td class="text-center text-nowrap">
                                    @if($a)
                                        <a href="{{ route('admin.manual.edit', $a) }}" class="btn btn-warning btn-action text-white" title="Edit">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <form action="{{ route('admin.manual.destroy', $a) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus catatan absensi ini?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-action" title="Hapus">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('admin.manual.create', ['date' => $date, 'user_id' => $g->id]) }}" class="btn btn-success btn-action text-white" title="Catat">
                                            <i class="bi bi-plus-lg"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.wa.compose', $g) }}" class="btn btn-secondary btn-action" title="WhatsApp">
                                        <i class="bi bi-whatsapp"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
