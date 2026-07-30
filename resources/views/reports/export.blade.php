<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            color: #4A4A4A;
            padding: 40px;
            background-color: #fff;
        }

        .header {
            border-bottom: 2px solid #C8102E;
            padding-bottom: 20px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand-title {
            font-size: 24px;
            font-weight: 700;
            color: #1E1E1E;
            margin: 0;
        }

        .report-title {
            font-size: 18px;
            font-weight: 600;
            color: #C8102E;
            margin: 5px 0 0 0;
        }

        .metadata {
            font-size: 13px;
            color: #777;
        }

        .summary-boxes {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .summary-box {
            flex: 1;
            border: 1px solid #D9D9D9;
            border-radius: 8px;
            padding: 15px;
            background-color: #F5F5F5;
        }

        .summary-box-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #777;
        }

        .summary-box-value {
            font-size: 20px;
            font-weight: 700;
            color: #1E1E1E;
            margin-top: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th {
            background-color: #F5F5F5;
            color: #1E1E1E;
            font-weight: 600;
            text-align: left;
            padding: 12px;
            font-size: 12px;
            border-bottom: 2px solid #D9D9D9;
        }

        td {
            padding: 12px;
            font-size: 13px;
            border-bottom: 1px solid #EAEAEA;
        }

        .text-right {
            text-align: right;
        }

        .btn-print {
            background-color: #C8102E;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            float: right;
            margin-bottom: 40px;
        }

        @media print {
            .btn-print {
                display: none;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

<button class="btn-print" onclick="window.print();">Print Report (PDF)</button>

<div class="header">
    <div>
        <h1 class="brand-title">OHAIYO JAPAN SURPLUS</h1>
        <h2 class="report-title">{{ $title }}</h2>
    </div>
    <div class="metadata text-right">
        <div><strong>Branch:</strong> {{ $branchName }}</div>
        <div><strong>Generated:</strong> {{ now()->format('Y-m-d h:i A') }}</div>
        <div><strong>Authorized:</strong> {{ auth()->user()->name }}</div>
    </div>
</div>

<div class="summary-boxes">
    <div class="summary-box">
        <div class="summary-box-label">Gross Revenue</div>
        <div class="summary-box-value">₱{{ number_format($totalSales, 2) }}</div>
    </div>
    <div class="summary-box">
        <div class="summary-box-label">Total Transactions</div>
        <div class="summary-box-value">{{ $sales->count() }} sales</div>
    </div>
    <div class="summary-box">
        <div class="summary-box-label">Estimated Profits</div>
        <div class="summary-box-value" style="color: #C8102E;">₱{{ number_format($totalProfit, 2) }}</div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>Reference Number</th>
            <th>Branch</th>
            <th>Cashier</th>
            <th>Date & Time</th>
            <th class="text-right">Amount</th>
        </tr>
    </thead>
    <tbody>
        @forelse($sales as $sale)
            <tr>
                <td><strong>{{ $sale->reference_number }}</strong></td>
                <td>{{ $sale->branch->name }}</td>
                <td>{{ $sale->user->name }}</td>
                <td>{{ $sale->created_at->format('Y-m-d H:i') }}</td>
                <td class="text-right"><strong>₱{{ number_format($sale->total_amount, 2) }}</strong></td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">No sales recorded.</td>
            </tr>
        @endforelse
    </tbody>
</table>

</body>
</html>
