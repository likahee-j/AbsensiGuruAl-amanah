<x-app-layout>
    <x-slot name="header">Rekapitulasi Absensi</x-slot>

    @push('styles')
    <style>
        .info-card {
            background: #fbf6e3; border: 1px solid #ece3bf; border-radius: .5rem;
            padding: .85rem 1.1rem; font-size: .9rem;
        }
        .info-card .lbl { display: inline-block; width: 140px; color: #5b5b46; font-weight: 600; }
    </style>
    @endpush

    {{-- Info --}}
    <div class="info-card mb-3">
        <div><span class="lbl">Tahun Pelajaran</span>: {{ $tapel?->tahun ?? '—' }}</div>
        <div><span class="lbl">Semester</span>: {{ $tapel?->semester ?? '—' }}</div>
        <div><span class="lbl">Tanggal Efektif</span>:
            @if($tapel)
                {{ $tapel->mulai->translatedFormat('d F Y') }} - {{ $tapel->selesai->translatedFormat('d F Y') }}
            @else — @endif
        </div>
    </div>

    {{-- Filter --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.rekap.index') }}" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Jenis Rekapitulasi <span class="text-danger">*</span></label>
                    <select name="jenis" id="jenis" class="form-select">
                        <option value="bulan" @selected($jenis === 'bulan')>Per-Bulan</option>
                        <option value="semester" @selected($jenis === 'semester')>Per-Semester</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Bulan <span class="text-danger">*</span></label>
                    <select name="bulan" id="bulan" class="form-select" {{ $jenis === 'semester' ? 'disabled' : '' }}>
                        @foreach($months as $value => $label)
                            <option value="{{ $value }}" @selected($value === $bulan)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-primary"><i class="bi bi-funnel"></i> Tampilkan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Result --}}
    <div class="card">
        <div class="card-header section-header">
            <span>Hasil Rekapitulasi — {{ $periodLabel }}</span>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.rekap.print', ['jenis' => $jenis, 'bulan' => $bulan]) }}"
                   target="_blank" class="btn btn-light text-dark"><i class="bi bi-printer"></i> Print</a>
                <a href="{{ route('admin.rekap.export.excel', ['jenis' => $jenis, 'bulan' => $bulan]) }}"
                   class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Excel</a>
                <a href="{{ route('admin.rekap.export.pdf', ['jenis' => $jenis, 'bulan' => $bulan]) }}"
                   class="btn btn-danger"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle text-center mb-0 table-dark-head">
                    <thead>
                        <tr>
                            <th style="width:55px">#</th>
                            <th class="text-start">Nama Guru</th>
                            <th>Hadir</th>
                            <th>Terlambat</th>
                            <th>Izin</th>
                            <th>Sakit</th>
                            <th>Alpa</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $i => $row)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td class="text-start fw-medium">{{ $row['guru']->name }}</td>
                                <td>{{ $row['hadir'] }}</td>
                                <td>{{ $row['terlambat'] }}</td>
                                <td>{{ $row['izin'] }}</td>
                                <td>{{ $row['sakit'] }}</td>
                                <td>{{ $row['alpa'] }}</td>
                                <td class="fw-bold">{{ $row['total'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-muted py-3">Belum ada data guru.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('jenis').addEventListener('change', function () {
            document.getElementById('bulan').disabled = this.value === 'semester';
        });
    </script>
    @endpush
</x-app-layout>
