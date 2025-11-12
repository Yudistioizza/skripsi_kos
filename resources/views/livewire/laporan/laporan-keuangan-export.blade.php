<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan</title>
    <style>
        * { box-sizing: border-box; }
        
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            color: #000;
            background: #fff;
            padding: 20px;
        }

        h2 {
            margin: 0 0 5px 0;
            font-size: 18px;
            font-weight: bold;
        }

        .meta {
            margin: 0 0 20px 0;
            font-size: 11px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        th, td {
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
            border: 1px solid #000;
        }

        th {
            font-weight: bold;
            background: #f0f0f0;
        }

        tbody tr:nth-child(even) {
            background: #fafafa;
        }

        .total-box {
            margin-top: 20px;
            font-weight: bold;
            font-size: 12px;
        }

        .total-box table {
            width: 50%;
            margin-left: auto;
        }

        .total-box td {
            border: none;
            padding: 4px 8px;
        }

        .total-box td:last-child {
            text-align: right;
        }

        @media print {
            body { padding: 0; }
            th { background: #e0e0e0 !important; -webkit-print-color-adjust: exact; }
            tbody tr:nth-child(even) { background: #f5f5f5 !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>

<h2>LAPORAN KEUANGAN</h2>
<p class="meta">Periode: {{ $startDate }} s/d {{ $endDate }}</p>

<table>
    <thead>
        <tr>
            <th>TANGGAL</th>
            <th>KODE TRANSAKSI</th>
            <th>PENGHUNI</th>
            <th>KAMAR</th>
            <th style="text-align:right">JUMLAH</th>
            <th style="text-align:center">STATUS</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transaksi as $trx)
            <tr>
                <td>{{ $trx->created_at->format('d/m/Y') }}</td>
                <td>{{ $trx->kode_transaksi }}</td>
                <td>{{ $trx->penghuni->nama ?? '-' }}</td>
                <td>{{ $trx->room->nomor_kamar ?? '-' }}</td>
                <td style="text-align:right">{{ number_format($trx->jumlah, 0, ',', '.') }}</td>
                <td style="text-align:center">{{ strtoupper($trx->status) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="total-box">
    <table>
        <tr>
            <td>TOTAL PENERIMAAN</td>
            <td>Rp {{ number_format($total, 0, ',', '.') }}</td>
        </tr>
    </table>
</div>

</body>
</html>