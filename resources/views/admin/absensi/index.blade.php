<x-app-layout>
    <x-slot name="header">Data Absensi</x-slot>

    @push('styles')
    <style>
        .info-card {
            background: #fbf6e3; border: 1px solid #ece3bf; border-radius: .5rem;
            padding: .85rem 1.1rem; font-size: .9rem;
        }
        .info-card .lbl { display: inline-block; width: 130px; color: #5b5b46; font-weight: 600; }

        .grid-wrap { overflow-x: auto; border: 1px solid #d7dce1; border-radius: .4rem; }
        table.absensi-grid { border-collapse: collapse; width: max-content; min-width: 100%; font-size: .72rem; }
        table.absensi-grid th, table.absensi-grid td {
            border: 1px solid #cdd4da; text-align: center; padding: 0; height: 26px;
        }
        table.absensi-grid thead th { background: #2a9d4a; color: #fff; font-weight: 600; }
        table.absensi-grid thead th.holiday { background: #e74c3c; }
        table.absensi-grid thead th.sum-head { background: #34495e; }
        .col-name {
            position: sticky; left: 0; z-index: 3; background: #343a40 !important; color: #fff;
            min-width: 190px; max-width: 190px; text-align: left !important; padding: .35rem .6rem !important;
        }
        tbody .col-name { background: #fff !important; color: #1f2937; font-weight: 600; }
        .day-num { min-width: 46px; }
        .mp-head { width: 23px; }
        .cell { width: 23px; height: 26px; cursor: pointer; }
        .cell.locked { cursor: not-allowed; }
        .cell-empty { background: #f1f3f5; }
        .cell-hadir { background: #2ecc71; color: #fff; }
        .cell-terlambat { background: #f1c40f; color: #5a4a00; }
        .cell-izin { background: #3498db; color: #fff; }
        .cell-sakit { background: #1abc9c; color: #fff; }
        .cell-alpa { background: #e74c3c; color: #fff; }
        .cell-holiday { background: #e74c3c; }
        .sum-cell { width: 30px; font-weight: 600; background: #f8f9fa; }
        .legend span { display: inline-flex; align-items: center; gap: .3rem; margin-right: .9rem; font-size: .8rem; }
        .legend i { width: 14px; height: 14px; border-radius: 3px; display: inline-block; }
    </style>
    @endpush

    {{-- Info --}}
    <div class="info-card mb-3">
        <div><span class="lbl">Tahun Pelajaran</span>: {{ $tapel?->tahun ?? '—' }}</div>
        <div><span class="lbl">Semester</span>: {{ $tapel?->semester ?? '—' }}</div>
        <div><span class="lbl">Bulan</span>: {{ $monthLabel }}</div>
    </div>

    {{-- Month filter --}}
    <form method="GET" class="row g-2 align-items-end mb-3" style="max-width:420px">
        <div class="col">
            <label class="form-label small text-muted mb-1">Pilih Bulan</label>
            <select name="bulan" class="form-select form-select-sm" onchange="this.form.submit()">
                @foreach($months as $value => $label)
                    <option value="{{ $value }}" @selected($value === $selected)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </form>

    <div class="legend mb-2">
        <span><i class="cell-hadir"></i> Hadir</span>
        <span><i class="cell-terlambat"></i> Terlambat</span>
        <span><i class="cell-izin"></i> Izin</span>
        <span><i class="cell-sakit"></i> Sakit</span>
        <span><i class="cell-alpa"></i> Alpa</span>
        <span><i class="cell-holiday"></i> Libur</span>
        <span class="text-muted">M = Masuk, P = Pulang</span>
    </div>

    <div class="grid-wrap">
        <table class="absensi-grid">
            <thead>
                <tr>
                    <th rowspan="2" class="col-name">Nama Guru</th>
                    @foreach($days as $day)
                        <th colspan="2" class="day-num {{ $day['is_holiday'] ? 'holiday' : '' }}"
                            title="{{ $day['holiday_label'] }}">{{ $day['num'] }}</th>
                    @endforeach
                    <th colspan="3" class="sum-head">Jumlah</th>
                </tr>
                <tr>
                    @foreach($days as $day)
                        <th class="mp-head {{ $day['is_holiday'] ? 'holiday' : '' }}">M</th>
                        <th class="mp-head {{ $day['is_holiday'] ? 'holiday' : '' }}">P</th>
                    @endforeach
                    <th class="sum-head">S</th>
                    <th class="sum-head">I</th>
                    <th class="sum-head">A</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    @php($guru = $row['guru'])
                    <tr>
                        <td class="col-name">{{ $guru->name }}</td>
                        @foreach($days as $day)
                            @php($rec = $row['records'][$day['date']] ?? null)
                            @if($day['is_holiday'])
                                <td class="cell cell-holiday locked"></td>
                                <td class="cell cell-holiday locked"></td>
                            @else
                                @php($mClass = $rec ? match($rec->status) {
                                    'hadir' => 'cell-hadir', 'terlambat' => 'cell-terlambat',
                                    'izin' => 'cell-izin', 'sakit' => 'cell-sakit',
                                    default => 'cell-alpa',
                                } : 'cell-empty')
                                @php($mLetter = $rec ? match($rec->status) {
                                    'hadir' => 'H', 'terlambat' => 'T', 'izin' => 'I',
                                    'sakit' => 'S', 'alpa' => 'A', 'tidak_hadir' => 'A', default => '',
                                } : '')
                                @php($pDone = $rec && $rec->check_out_time)
                                <td class="cell {{ $mClass }} att-cell"
                                    data-user="{{ $guru->id }}" data-name="{{ $guru->name }}"
                                    data-date="{{ $day['date'] }}"
                                    data-status="{{ $rec->status ?? '' }}"
                                    data-cin="{{ $rec && $rec->check_in_time ? substr($rec->check_in_time,0,5) : '' }}"
                                    data-cout="{{ $rec && $rec->check_out_time ? substr($rec->check_out_time,0,5) : '' }}"
                                    data-notes="{{ $rec->notes ?? '' }}">{{ $mLetter }}</td>
                                <td class="cell {{ $pDone ? 'cell-hadir' : ($rec && in_array($rec->status,['izin','sakit','alpa','tidak_hadir']) ? $mClass : 'cell-empty') }} att-cell"
                                    data-user="{{ $guru->id }}" data-name="{{ $guru->name }}"
                                    data-date="{{ $day['date'] }}"
                                    data-status="{{ $rec->status ?? '' }}"
                                    data-cin="{{ $rec && $rec->check_in_time ? substr($rec->check_in_time,0,5) : '' }}"
                                    data-cout="{{ $rec && $rec->check_out_time ? substr($rec->check_out_time,0,5) : '' }}"
                                    data-notes="{{ $rec->notes ?? '' }}">{{ $pDone ? '✓' : '' }}</td>
                            @endif
                        @endforeach
                        <td class="sum-cell">{{ $row['summary']['S'] }}</td>
                        <td class="sum-cell">{{ $row['summary']['I'] }}</td>
                        <td class="sum-cell">{{ $row['summary']['A'] }}</td>
                    </tr>
                @empty
                    <tr><td class="col-name">—</td><td colspan="{{ count($days) * 2 + 3 }}" class="text-muted py-3">Belum ada data guru.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p class="text-muted small mt-2"><i class="bi bi-info-circle"></i> Klik sel pada hari kerja untuk mengisi atau mengubah absensi.</p>

    {{-- Edit cell modal --}}
    <div class="modal fade" id="cellModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.absensi.cell') }}" class="modal-content">
                @csrf
                <input type="hidden" name="user_id" id="cellUser">
                <input type="hidden" name="date" id="cellDate">
                <div class="modal-header">
                    <h5 class="modal-title">Input Absensi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3"><strong id="cellName"></strong><br>
                        <span class="text-muted small" id="cellDateLabel"></span></p>
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" id="cellStatus" class="form-select" required>
                            @foreach($statuses as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label">Jam Masuk</label>
                            <input type="time" name="check_in_time" id="cellCin" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Jam Pulang</label>
                            <input type="time" name="check_out_time" id="cellCout" class="form-control">
                        </div>
                    </div>
                    <div class="mb-1">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" id="cellNotes" rows="2" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        (function () {
            const modal = new bootstrap.Modal(document.getElementById('cellModal'));
            document.querySelectorAll('.att-cell').forEach(function (cell) {
                cell.addEventListener('click', function () {
                    document.getElementById('cellUser').value = cell.dataset.user;
                    document.getElementById('cellDate').value = cell.dataset.date;
                    document.getElementById('cellName').textContent = cell.dataset.name;
                    document.getElementById('cellDateLabel').textContent =
                        new Date(cell.dataset.date + 'T00:00:00').toLocaleDateString('id-ID',
                            { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
                    document.getElementById('cellStatus').value = cell.dataset.status || 'hadir';
                    document.getElementById('cellCin').value = cell.dataset.cin || '';
                    document.getElementById('cellCout').value = cell.dataset.cout || '';
                    document.getElementById('cellNotes').value = cell.dataset.notes || '';
                    modal.show();
                });
            });
        })();
    </script>
    @endpush
</x-app-layout>
