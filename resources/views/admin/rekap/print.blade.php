<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekapitulasi Absensi - {{ $periodLabel }}</title>
    <style>
        * { font-family: DejaVu Sans, Arial, sans-serif; }
        body { margin: 24px; color: #1f2937; font-size: 12px; }
        .head { text-align: center; border-bottom: 3px double #333; padding-bottom: 8px; margin-bottom: 14px; }
        .head h1 { font-size: 16px; margin: 0; text-transform: uppercase; }
        .head h2 { font-size: 13px; margin: 2px 0 0; font-weight: normal; }
        .meta { margin: 10px 0; font-size: 12px; }
        .meta td { padding: 1px 4px; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.data th, table.data td { border: 1px solid #555; padding: 5px 6px; }
        table.data th { background: #e9ecef; text-align: center; }
        table.data td.c { text-align: center; }
        .sign { margin-top: 36px; width: 100%; }
        .sign td { width: 50%; text-align: center; vertical-align: top; font-size: 12px; }
        .toolbar { text-align: center; margin-bottom: 14px; }
        .toolbar button { padding: 6px 16px; cursor: pointer; }
        @media print { .toolbar { display: none; } body { margin: 0; } }
    </style>
</head>
<body>
    <div class="toolbar">
        <button onclick="window.print()">Cetak / Print</button>
    </div>

    <div class="head">
        <h1>Rekapitulasi Absensi Guru</h1>
        <h2>{{ $school->school_name ?? 'Sekolah' }}</h2>
        @if($school->address)<h2>{{ $school->address }}</h2>@endif
    </div>

    <table class="meta">
        <tr>
            <td><strong>Tahun Pelajaran</strong></td><td>: {{ $tapel?->tahun ?? '-' }}</td>
            <td style="width:40px"></td>
            <td><strong>Jenis Rekap</strong></td><td>: {{ $jenis === 'semester' ? 'Per-Semester' : 'Per-Bulan' }}</td>
        </tr>
        <tr>
            <td><strong>Semester</strong></td><td>: {{ $tapel?->semester ?? '-' }}</td>
            <td></td>
            <td><strong>Periode</strong></td><td>: {{ $periodLabel }}</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th style="width:35px">No</th>
                <th>Nama Guru</th>
                <th style="width:65px">Hadir</th>
                <th style="width:75px">Terlambat</th>
                <th style="width:55px">Izin</th>
                <th style="width:55px">Sakit</th>
                <th style="width:55px">Alpa</th>
                <th style="width:60px">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $i => $row)
                <tr>
                    <td class="c">{{ $i + 1 }}</td>
                    <td>{{ $row['guru']->name }}</td>
                    <td class="c">{{ $row['hadir'] }}</td>
                    <td class="c">{{ $row['terlambat'] }}</td>
                    <td class="c">{{ $row['izin'] }}</td>
                    <td class="c">{{ $row['sakit'] }}</td>
                    <td class="c">{{ $row['alpa'] }}</td>
                    <td class="c">{{ $row['total'] }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="c">Belum ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="sign">
        <tr>
            <td>Mengetahui,<br>Kepala Sekolah<br><br><br><br>(........................................)</td>
            <td>{{ now()->translatedFormat('d F Y') }}<br>Admin<br><br><br><br>(........................................)</td>
        </tr>
    </table>
</body>
</html>
