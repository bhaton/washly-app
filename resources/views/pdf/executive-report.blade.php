<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Eksekutif Operasional & Keuangan Washly</title>
    <style>
        @page {
            margin: 25pt 30pt;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 10pt;
            line-height: 1.4;
            background-color: #ffffff;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border-bottom: 2px solid #0284c7;
            padding-bottom: 10px;
        }
        .header-title {
            font-size: 18pt;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }
        .header-subtitle {
            font-size: 9pt;
            color: #64748b;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .meta-info {
            text-align: right;
            font-size: 8.5pt;
            color: #475569;
        }
        .section-title {
            font-size: 11pt;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 15px;
            margin-bottom: 8px;
            border-left: 4px solid #0284c7;
            padding-left: 8px;
        }
        .kpi-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px;
            margin-bottom: 15px;
        }
        .kpi-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 12px;
            text-align: center;
        }
        .kpi-label {
            font-size: 7.5pt;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
        }
        .kpi-value {
            font-size: 14pt;
            font-weight: 900;
            color: #0f172a;
            margin-top: 4px;
        }
        .kpi-value.green { color: #16a34a; }
        .kpi-value.blue { color: #0284c7; }
        .kpi-value.rose { color: #e11d48; }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 8.5pt;
        }
        .data-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 7.5pt;
            padding: 6px 8px;
            text-align: left;
        }
        .data-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 7pt;
            font-weight: 800;
            text-transform: uppercase;
        }
        .badge-success { background-color: #dcfce7; color: #15803d; }
        .badge-warning { background-color: #fef3c7; color: #b45309; }
        .badge-danger { background-color: #ffe4e6; color: #be123c; }
        .badge-info { background-color: #e0f2fe; color: #0369a1; }

        .signature-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }
        .signature-box {
            width: 45%;
            text-align: center;
            font-size: 8.5pt;
        }
        .signature-line {
            margin-top: 50px;
            border-bottom: 1px solid #0f172a;
            width: 70%;
            margin-left: auto;
            margin-right: auto;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 7.5pt;
            color: #94a3b8;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
        }
    </style>
</head>
<body>

    <!-- Header Block -->
    <table class="header-table">
        <tr>
            <td style="vertical-align: middle;">
                <div class="header-title">WASHLY LAUNDRY</div>
                <div class="header-subtitle">Laporan Eksekutif Operasional & Keuangan Outlet</div>
            </td>
            <td class="meta-info" style="vertical-align: middle;">
                <strong>Periode Laporan:</strong> {{ $periodLabel }}<br>
                <strong>Dicetak Pada:</strong> {{ $printedAt }}<br>
                <strong>Dicetak Oleh:</strong> {{ $printedBy }}
            </td>
        </tr>
    </table>

    <!-- Executive KPI Summary -->
    <div class="section-title">Ringkasan Eksekutif Operasional</div>
    <table class="kpi-table">
        <tr>
            <td class="kpi-card" style="width: 25%;">
                <div class="kpi-label">Total Omset / Pendapatan</div>
                <div class="kpi-value green">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</div>
            </td>
            <td class="kpi-card" style="width: 25%;">
                <div class="kpi-label">Total Transaksi Order</div>
                <div class="kpi-value blue">{{ $totalOrders }}</div>
            </td>
            <td class="kpi-card" style="width: 25%;">
                <div class="kpi-label">Order Selesai (Completed)</div>
                <div class="kpi-value green">{{ $completedOrders }}</div>
            </td>
            <td class="kpi-card" style="width: 25%;">
                <div class="kpi-label">Order Dalam Proses</div>
                <div class="kpi-value blue">{{ $inProgressOrders }}</div>
            </td>
        </tr>
    </table>

    <!-- Breakdown Tables Grid (Top Items & Driver Performance) -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
        <tr>
            <!-- Top Items Left -->
            <td style="width: 48%; vertical-align: top; padding-right: 2%;">
                <div class="section-title">5 Item Laundry Terlaris</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama Item</th>
                            <th style="text-align: center;">Penjualan</th>
                            <th style="text-align: right;">Total Omset</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topItems as $item)
                            <tr>
                                <td><strong>{{ $item->service_name }}</strong></td>
                                <td style="text-align: center;">{{ $item->total_qty }} pcs</td>
                                <td style="text-align: right; font-weight: bold;">Rp{{ number_format($item->total_revenue, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; color: #94a3b8;">Belum ada data transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </td>

            <!-- Driver Performance Right -->
            <td style="width: 48%; vertical-align: top; padding-left: 2%;">
                <div class="section-title">Performa Penugasan Driver</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama Driver</th>
                            <th style="text-align: center;">Pickup Selesai</th>
                            <th style="text-align: center;">Delivery Selesai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($drivers as $driver)
                            <tr>
                                <td><strong>{{ $driver->name }}</strong></td>
                                <td style="text-align: center; color: #0284c7; font-weight: bold;">{{ $driver->completed_pickups }} Task</td>
                                <td style="text-align: center; color: #16a34a; font-weight: bold;">{{ $driver->completed_deliveries }} Task</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; color: #94a3b8;">Belum ada data driver.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <!-- Detailed Transactions Table -->
    <div class="section-title">Rincian Transaksi Pesanan ({{ count($orders) }} Items)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>No. Order</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Layanan</th>
                <th>Status Pembayaran</th>
                <th>Status Operasional</th>
                <th style="text-align: right;">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                @php
                    $isPaid = $order->payment?->status === 'PAID' || in_array($order->payment?->status, ['SUCCESS', 'SETTLEMENT']);
                @endphp
                <tr>
                    <td><strong style="font-family: monospace;">{{ $order->order_number }}</strong></td>
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $order->customer->name ?? $order->pickup_name ?? '-' }}</td>
                    <td>{{ strtoupper($order->service_type ?? 'KILOAN') }} ({{ $order->package_type ?? 'Ekonomis' }})</td>
                    <td>
                        @if($isPaid)
                            <span class="badge badge-success">LUNAS</span>
                        @else
                            <span class="badge badge-warning">BELUM BAYAR</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-info">{{ str_replace('_', ' ', $order->status) }}</span>
                    </td>
                    <td style="text-align: right; font-weight: bold;">
                        Rp{{ number_format($order->total, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #94a3b8;">Tidak ada catatan transaksi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Executive Signatures Block for Boss / Management -->
    <table class="signature-table">
        <tr>
            <td class="signature-box">
                <div>Dibuat & Dilaporkan Oleh,</div>
                <div class="signature-line"></div>
                <div style="font-weight: bold; margin-top: 4px;">{{ $printedBy }}</div>
                <div style="font-size: 7.5pt; color: #64748b;">Administrator Operasional Washly</div>
            </td>
            <td style="width: 10%;"></td>
            <td class="signature-box">
                <div>Disetujui & Diterima Oleh,</div>
                <div class="signature-line"></div>
                <div style="font-weight: bold; margin-top: 4px;">Pemilik / Manager Operasional (Bos)</div>
                <div style="font-size: 7.5pt; color: #64748b;">Executive Management Washly Laundry</div>
            </td>
        </tr>
    </table>

    <!-- Footer Note -->
    <div class="footer">
        Laporan Eksekutif Dokumen Resmi Washly Laundry &bull; Dicetak secara otomatis oleh Sistem Manajemen Operasional
    </div>

</body>
</html>
