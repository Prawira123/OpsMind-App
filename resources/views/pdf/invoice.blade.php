<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->number }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 14px;
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            display: table;
            width: 100%;
            margin-bottom: 40px;
        }
        .header-left {
            display: table-cell;
            vertical-align: top;
        }
        .header-right {
            display: table-cell;
            text-align: right;
            vertical-align: top;
        }
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #4f46e5;
            margin-bottom: 5px;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .section-title {
            font-size: 12px;
            text-transform: uppercase;
            font-weight: bold;
            color: #999;
            margin-bottom: 10px;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 40px;
        }
        .info-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .table th {
            background-color: #f9fafb;
            border-bottom: 2px solid #e5e7eb;
            padding: 12px 10px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            color: #6b7280;
        }
        .table td {
            padding: 12px 10px;
            border-bottom: 1px solid #f3f4f6;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals {
            float: right;
            width: 250px;
        }
        .total-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .total-label {
            display: table-cell;
            text-align: left;
            color: #6b7280;
        }
        .total-value {
            display: table-cell;
            text-align: right;
            font-weight: bold;
        }
        .grand-total {
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
            margin-top: 10px;
            font-size: 18px;
            color: #4f46e5;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #f3f4f6;
            font-size: 12px;
            color: #9ca3af;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 10px;
        }
        .status-paid { background-color: #dcfce7; color: #15803d; }
        .status-draft { background-color: #f3f4f6; color: #4b5563; }
        .status-overdue { background-color: #fee2e2; color: #b91c1c; }
        .status-sent { background-color: #dbeafe; color: #1d4ed8; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-left">
                <div class="invoice-title">INVOICE</div>
                <div class="font-mono">{{ $invoice->number }}</div>
                @php
                    $statusClass = match($invoice->status) {
                        'paid' => 'Paid',
                        'draft' => 'Draft',
                        'overdue' => 'Overdue',
                        'send' => 'Unpaid',
                        'unpaid' => 'Unpaid',
                        default => 'Draft',
                    };
                @endphp
                <div class="status-badge {{ $statusClass }}">
                    {{ strtoupper($statusClass) }}
                </div>
            </div>
            <div class="header-right">
                <div class="company-name">{{ $tenant->name ?? 'Ops Mind' }}</div>
                <div>{{ $tenant->address ?? '' }}</div>
                <div>{{ $tenant->email ?? '' }}</div>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-col">
                <div class="section-title">Tagihan Untuk:</div>
                <div style="font-weight: bold; font-size: 16px;">{{ $invoice->client->name }}</div>
                <div>{{ $invoice->client->company }}</div>
                <div>{{ $invoice->client->email }}</div>
                <div>{{ $invoice->client->phone }}</div>
                <div style="max-width: 250px;">{{ $invoice->client->address }}</div>
            </div>
            <div class="info-col text-right">
                <div class="section-title">Detail Invoice:</div>
                <div><strong>Tanggal Terbit:</strong> {{ \Carbon\Carbon::parse($invoice->issue_date)->format('d M Y') }}</div>
                <div><strong>Jatuh Tempo:</strong> {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</div>
            </div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="45%">Item & Deskripsi</th>
                    <th width="10%" class="text-center">Qty</th>
                    <th width="20%" class="text-right">Harga</th>
                    <th width="20%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <div style="font-weight: bold;">{{ $item->name }}</div>
                        @if($item->description)
                            <div style="font-size: 12px; color: #6b7280;">{{ $item->description }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->total, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div class="total-row">
                <div class="total-label">Subtotal</div>
                <div class="total-value">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</div>
            </div>
            @if($invoice->tax > 0)
            <div class="total-row">
                <div class="total-label">Pajak ({{ $invoice->tax }}%)</div>
                <div class="total-value">Rp {{ number_format($invoice->subtotal * ($invoice->tax / 100), 0, ',', '.') }}</div>
            </div>
            @endif
            <div class="total-row grand-total">
                <div class="total-label">Total</div>
                <div class="total-value">Rp {{ number_format($invoice->total, 0, ',', '.') }}</div>
            </div>
        </div>

        <div style="clear: both;"></div>

        @if($invoice->notes)
        <div style="margin-top: 40px;">
            <div class="section-title">Catatan:</div>
            <div style="font-size: 13px; color: #4b5563;">{{ $invoice->notes }}</div>
        </div>
        @endif

        <div class="footer">
            <div>Terima kasih atas kerja sama Anda.</div>
            <div style="margin-top: 5px;">Invoice ini dibuat secara otomatis oleh sistem Ops Mind.</div>
        </div>
    </div>
</body>
</html>
