<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --clr-primary: #2563eb;
            --clr-primary-light: #eff6ff;
            --clr-success: #10b981;
            --clr-danger: #ef4444;
            --clr-gray: #6b7280;
            --clr-bg: #f9fafb;
            --radius: 8px;
            --shadow: 0 2px 6px rgba(0,0,0,.08);
            --font: 'Inter', sans-serif;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: var(--font);
            font-size: 14px;
            color: #111827;
            background: var(--clr-bg);
            padding: 40px;
        }

        h2, h3 { margin: 0 0 8px; font-weight: 600; }
        h2 { font-size: 24px; }
        h3 { font-size: 18px; }

        .card {
            background: #fff;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 24px;
            margin-bottom: 24px;
        }

        .meta {
            color: var(--clr-gray);
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        thead { background: var(--clr-primary-light); }
        th, td { padding: 12px 16px; text-align: left; }
        th {
            font-weight: 600;
            color: var(--clr-primary);
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: .5px;
        }
        tbody tr {
            border-bottom: 1px solid #e5e7eb;
            transition: background .15s;
        }
        tbody tr:hover { background: #f3f4f6; }
        tbody tr:last-child { border: none; }

        .status {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 500;
            text-transform: capitalize;
        }
        .status.lunas { background: #d1fae5; color: #065f46; }
        .status.belum-bayar { background: #fee2e2; color: #991b1b; }

        .total-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--clr-primary);
            color: #fff;
            padding: 16px 24px;
            border-radius: var(--radius);
            font-size: 18px;
            font-weight: 600;
        }
        .total-box span:first-child { opacity: .9; }

        @media print {
            body { background: #fff; padding: 0; }
            .card { box-shadow: none; padding: 0; }
            .total-box { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>

<div class="card">
    <h2>Laporan Keuangan</h2>
    <p class="meta">Periode: {{ $startDate }} s/d {{ $endDate }}</p>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kode Transaksi</th>
                <th>Penghuni</th>
                <th>Kamar</th>
                <th style="text-align:right">Jumlah</th>
                <th style="text-align:center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksi as $trx)
                <tr>
                    <td>{{ $trx->created_at->format('d/m/Y') }}</td>
                    <td>{{ $trx->kode_transaksi }}</td>
                    <td>{{ $trx->penghuni->nama ?? '-' }}</td>
                    <td>{{ $trx->room->nomor_kamar ?? '-' }}</td>
                    <td style="text-align:right">Rp {{ number_format($trx->jumlah, 0, ',', '.') }}</td>
                    <td style="text-align:center">
                        <span class="status {{ $trx->status === 'lunas' ? 'lunas' : 'belum-bayar' }}">
                            {{ ucfirst($trx->status) }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="total-box">
    <span>Total Penerimaan</span>
    <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
</div>

</body>
</html>