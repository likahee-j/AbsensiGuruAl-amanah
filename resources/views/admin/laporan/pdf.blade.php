<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Presensi — {{ $guru->name }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
        h1 { font-size: 16px; margin: 0 0 4px 0; }
        .meta { font-size: 10px; color: #555; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th, td { border: 1px solid #d0d0d0; padding: 6px 8px; text-align: left; }
        th { background: #f3f4f6; font-size: 10px; text-transform: uppercase; }
        .summary { margin-bottom: 12px; }
        .summary .stat { display: inline-block; border: 1px solid #d0d0d0; padding: 4px 8px; border-radius: 3px; margin-right: 4px; margin-bottom: 4px; font-size: 10px; }
    </style>
</head>
<body>
    <h1>Laporan Presensi Guru</h1>
    <div class="meta">
        <strong>{{ $guru->name }}</strong> — {{ $guru->email }}
        @if($guru->phone) — {{ $guru->phone }} @endif
        <br>Periode: {{ $filters['from'] }} s/d {{ $filters['to'] }}
        <br>Dicetak: {{ now()->format('Y-m-d H:i') }}
    </div>

    <div class="summary">
        @foreach(\App\Models\Attendance::STATUS_LABELS as $key => $label)
            <span class="stat">{{ $label }}: <strong>{{ $summary[$key] ?? 0 }}</strong></span>
        @endforeach
        <span class="stat">Total: <strong>{{ $summary['total'] ?? 0 }}</strong></span>
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
</body>
</html>
