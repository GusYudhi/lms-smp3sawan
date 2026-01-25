<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Jurnal Mengajar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2, .header h3 {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-center {
            text-align: center;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print">
        <button onclick="window.print()">Cetak</button>
        <button onclick="window.close()">Tutup</button>
    </div>

    <div class="header">
        <h2>SMPN 3 SAWAN</h2>
        <h3>JURNAL MENGAJAR GURU</h3>
        <p>Bulan: {{ date('F', mktime(0, 0, 0, $bulan, 10)) }} {{ $tahun }}</p>
    </div>

    <p><strong>Nama Guru:</strong> {{ $guru->name }}</p>
    <p><strong>NIP:</strong> {{ $guru->nomor_induk }}</p>

    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="12%" class="text-center">Tanggal</th>
                <th width="10%" class="text-center">Jam Ke</th>
                <th width="10%" class="text-center">Kelas</th>
                <th width="20%" class="text-center">Mata Pelajaran</th>
                <th width="43%" class="text-center">Materi & Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jurnals as $index => $jurnal)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($jurnal->tanggal)->format('d/m/Y') }}<br>{{ $jurnal->hari }}</td>
                <td class="text-center">{{ $jurnal->jam_ke_mulai }} - {{ $jurnal->jam_ke_selesai }}</td>
                <td class="text-center">{{ $jurnal->kelas->nama_kelas }}</td>
                <td>{{ $jurnal->mataPelajaran->nama_pelajaran }}</td>
                <td>
                    <strong>Materi:</strong> {{ $jurnal->materi_pembelajaran }}<br>
                    @if($jurnal->keterangan)
                    <strong>Ket:</strong> {{ $jurnal->keterangan }}
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada data jurnal pada bulan ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <table style="border: none; margin-top: 50px;">
        <tr style="border: none;">
            <td style="border: none; width: 70%;"></td>
            <td style="border: none; text-align: center;">
                Sawan, {{ date('d F Y') }}<br>
                Guru Mata Pelajaran,<br><br><br><br>
                <strong>{{ $guru->name }}</strong><br>
                NIP. {{ $guru->nomor_induk }}
            </td>
        </tr>
    </table>
</body>
</html>
