<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Absensi</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 18px;
        }

        body {
            color: #111827;
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
        }

        h1 {
            font-size: 18px;
            margin: 0 0 6px;
        }

        p {
            color: #4b5563;
            margin: 0 0 16px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 7px;
            text-align: left;
        }

        th {
            background: #f3f4f6;
        }

        tr {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <h1>Laporan Absensi</h1>
    <p>Periode {{ \Illuminate\Support\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Illuminate\Support\Carbon::parse($endDate)->format('d/m/Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>Karyawan</th>
                <th>Departemen</th>
                <th>Team</th>
                <th>Tanggal</th>
                <th>Masuk</th>
                <th>Pulang</th>
                <th>Lembur</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($attendances as $attendance)
                <tr>
                    <td>{{ $attendance->karyawan?->nama_karyawan ?? '-' }}</td>
                    <td>{{ $attendance->karyawan?->team?->departemen?->nama_departemen ?? '-' }}</td>
                    <td>{{ $attendance->karyawan?->team?->nama_team ?? '-' }}</td>
                    <td>{{ $attendance->tanggal?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $attendance->waktu_masuk?->format('H:i') ?? '-' }}</td>
                    <td>{{ $attendance->waktu_pulang?->format('H:i') ?? '-' }}</td>
                    <td>{{ $attendance->is_overtime ? 'Ya' : 'Tidak' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Tidak ada data absensi pada rentang tanggal ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
