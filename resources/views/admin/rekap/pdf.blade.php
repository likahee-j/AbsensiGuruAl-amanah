<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Absensi</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
        h1 { font-size: 16px; margin: 0 0 4px 0; }
        .meta { font-size: 10px; margin-bottom: 12px; color: #555; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d0d0d0; padding: 6px 8px; text-align: left; }
        th { background: #f3f4f6; font-size: 10px; text-transform: uppercase; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 9px; }
        .badge-hadir { background: #dcfce7; color: #166534; }
        .badge-terlambat { background: #fef9c3; color: #854d0e; }
        .badge-tidak_hadir { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <h1>Rekap Absensi Guru</h1>
    <div class="meta">
        Periode: {{ $filters['from'] }} s/d {{ $filters['to'] }}
        @if($filters['user_id'])
            — Guru ID #{{ $filters['user_id'] }}
        @endif
        <br>Dicetak: {{ now()->format('Y-m-d H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Nama Guru</th>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row->user->name ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->date)->format('Y-m-d') }}</td>
                    <td>{{ $row->check_in_time ?: '-' }}</td>
                    <td>
                        <span class="badge badge-{{ $row->status }}">{{ ucfirst(str_replace('_', ' ', $row->status)) }}</span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center; color:#888;">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
