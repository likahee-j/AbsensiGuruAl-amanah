<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Presensi — {{ $guru->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #222; padding: 24px; }
        h1 { font-size: 18px; margin: 0 0 4px 0; }
        .meta { font-size: 11px; color: #555; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #d0d0d0; padding: 6px 8px; text-align: left; }
        th { background: #f3f4f6; font-size: 11px; text-transform: uppercase; }
        .summary { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
        .stat { border: 1px solid #d0d0d0; padding: 6px 10px; border-radius: 4px; }
        .stat strong { font-size: 14px; }
        .actions { margin-top: 16px; }
        @media print { .actions { display: none; } body { padding: 12px; } }
    </style>
</head>
<body>
    <h1>Laporan Presensi Guru</h1>
    <div class="meta">
        <strong>{{ $guru->name }}</strong> &middot; {{ $guru->email }}
        @if($guru->phone) &middot; {{ $guru->phone }} @endif
        <br>Periode: {{ $filters['from'] }} s/d {{ $filters['to'] }}
        <br>Dicetak: {{ now()->translatedFormat('d F Y H:i') }}
    </div>

    <div class="summary">
        @foreach(\App\Models\Attendance::STATUS_LABELS as $key => $label)
            <div class="stat">{{ $label }}: <strong>{{ $summary[$key] ?? 0 }}</strong></div>
        @endforeach
        <div class="stat">Total: <strong>{{ $summary['total'] ?? 0 }}</strong></div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:30px;">No</th>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Jam Pulang</th>
                <th>Status</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $i => $r)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($r->date)->format('Y-m-d') }}</td>
                    <td>{{ $r->check_in_time ?: '-' }}</td>
                    <td>{{ $r->check_out_time ?: '-' }}</td>
                    <td>{{ \App\Models\Attendance::STATUS_LABELS[$r->status] ?? $r->status }}</td>
                    <td>{{ $r->notes ?: '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center; color:#888;">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="actions">
        <button onclick="window.print()">Cetak</button>
        <a href="{{ route('admin.laporan.show', array_merge(['guru' => $guru], $filters)) }}">← Kembali</a>
    </div>
</body>
</html>
