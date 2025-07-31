<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Riwayat Peminjaman Barang</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm 15mm; /* Kurangi margin untuk ruang lebih banyak */
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            font-size: 10pt; /* Ukuran font lebih kecil untuk menghemat ruang */
        }
        h1 {
            text-align: center;
            color: #1e3a8a;
            margin-bottom: 10px;
        }
        h2 {
            color: #3b82f6;
            margin-top: 15px;
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            table-layout: fixed; /* Pastikan tabel menyesuaikan lebar */
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px; /* Kurangi padding untuk ruang lebih banyak */
            text-align: left;
            word-wrap: break-word; /* Memecah teks panjang */
            overflow-wrap: break-word;
        }
        th {
            background-color: #1e3a8a;
            color: white;
            font-size: 10pt;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .filters, .stats {
            margin-bottom: 10px;
        }
        .filters table, .stats table {
            width: 50%;
            font-size: 10pt;
        }
        .filters td, .stats td {
            padding: 4px;
        }
    </style>
</head>
<body>
    <h1>Laporan Riwayat Peminjaman Barang</h1>
    <p style="text-align: center;">Tanggal Cetak: {{ now()->format('d/m/Y H:i:s') }}</p>

    <h2>Informasi Filter</h2>
    <table class="filters">
        @foreach($filters as $key => $value)
            <tr>
                <td><strong>{{ $key }}:</strong></td>
                <td>{{ $value }}</td>
            </tr>
        @endforeach
    </table>

    <h2>Statistik</h2>
    <table class="stats">
        <tr><td><strong>Total Transaksi:</strong></td><td>{{ $stats['total'] }}</td></tr>
        <tr><td><strong>Sedang Dipinjam:</strong></td><td>{{ $stats['borrowed'] }}</td></tr>
        <tr><td><strong>Sudah Dikembalikan:</strong></td><td>{{ $stats['returned'] }}</td></tr>
        <tr><td><strong>Terlambat Kembali:</strong></td><td>{{ $stats['overdue'] }}</td></tr>
    </table>

    <h2>Daftar Riwayat Peminjaman</h2>
    @if($borrowings->isEmpty())
        <p>Tidak ada data peminjaman yang sesuai dengan filter.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Nama Peminjam</th>
                    <th>Tanggal Pinjam</th>
                    <th>Tanggal Kembali</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Lama Pinjam</th>
                </tr>
            </thead>
            <tbody>
                @foreach($borrowings as $index => $borrowing)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $borrowing->barang->nama_barang }} ({{ $borrowing->barang->kategori ?? 'Tanpa Kategori' }})</td>
                        <td>{{ $borrowing->nama_peminjam }}</td>
                        <td>{{ $borrowing->tanggal_pinjam->format('d/m/Y') }}</td>
                        <td>{{ $borrowing->tanggal_kembali ? $borrowing->tanggal_kembali->format('d/m/Y') : 'Belum Dikembalikan' }}</td>
                        <td>{{ $borrowing->jumlah }} unit</td>
                        <td>
                            @if($borrowing->status == 'dipinjam')
                                @php
                                    $days = $borrowing->tanggal_pinjam->diffInDays(now());
                                    $isOverdue = $days > 7;
                                @endphp
                                {{ $isOverdue ? 'Terlambat' : 'Sedang Dipinjam' }}
                            @else
                                Sudah Dikembalikan
                            @endif
                        </td>
                        <td>
                            @php
                                $endDate = $borrowing->tanggal_kembali ?? now();
                                $duration = $borrowing->tanggal_pinjam->diffInDays($endDate);
                            @endphp
                            {{ $duration }} hari
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
