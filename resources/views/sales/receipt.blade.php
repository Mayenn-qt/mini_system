<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $sale->reference_number }}</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Courier+Prime:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Courier Prime', monospace;
            background-color: #f7f7f7;
            padding: 30px 10px;
            color: #333;
        }

        .receipt-box {
            max-width: 360px;
            margin: 0 auto;
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        .text-center {
            text-align: center;
        }

        .brand-name {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .receipt-header {
            margin-bottom: 20px;
            border-bottom: 1px dashed #ccc;
            padding-bottom: 15px;
        }

        .receipt-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            font-size: 13px;
        }

        .receipt-divider {
            border-top: 1px dashed #ccc;
            margin: 15px 0;
        }

        .receipt-total {
            font-size: 16px;
            font-weight: bold;
        }

        .btn-print {
            display: block;
            width: 100%;
            max-width: 360px;
            margin: 20px auto 0 auto;
            padding: 10px;
            background-color: #1e1e1e;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        @media print {
            .btn-print {
                display: none;
            }
            body {
                background-color: white;
                padding: 0;
            }
            .receipt-box {
                box-shadow: none;
                border: none;
                max-width: 100%;
                padding: 0;
            }
        }
    </style>
</head>
<body>

<div class="receipt-box">
    <div class="text-center receipt-header">
        <h2 class="brand-name">OHAIYO JAPAN SURPLUS</h2>
        <div style="font-size: 11px; margin-top: 4px; color:#666;">
            {{ \App\Models\Setting::get('store_address', 'Rizal Street, Sorsogon City') }} <br>
            Tel: {{ \App\Models\Setting::get('store_phone', '+63 912 345 6789') }}
        </div>
        <div class="receipt-divider"></div>
        <div class="receipt-row">
            <span>Ref No:</span>
            <strong>{{ $sale->reference_number }}</strong>
        </div>
        <div class="receipt-row">
            <span>Branch:</span>
            <span>{{ $sale->branch->name }}</span>
        </div>
        <div class="receipt-row">
            <span>Date:</span>
            <span>{{ $sale->created_at->format('Y-m-d H:i') }}</span>
        </div>
        <div class="receipt-row">
            <span>Cashier:</span>
            <span>{{ $sale->user->name }}</span>
        </div>
    </div>

    <!-- Items -->
    <div>
        <div class="receipt-row" style="font-weight: bold; margin-bottom: 10px;">
            <span style="width: 50%;">ITEM</span>
            <span style="width: 20%; text-align: center;">QTY</span>
            <span style="width: 30%; text-align: right;">PRICE</span>
        </div>
        @foreach($sale->items as $item)
            <div class="receipt-row">
                <span style="width: 50%;">{{ $item->product->name }}</span>
                <span style="width: 20%; text-align: center;">{{ $item->quantity }}</span>
                <span style="width: 30%; text-align: right;">₱{{ number_format($item->subtotal, 2) }}</span>
            </div>
        @endforeach
    </div>

    <div class="receipt-divider"></div>

    <!-- Calculations -->
    <div class="receipt-row">
        <span>Subtotal</span>
        <span>₱{{ number_format($sale->total_amount, 2) }}</span>
    </div>
    <div class="receipt-row receipt-total">
        <span>TOTAL BILL</span>
        <span>₱{{ number_format($sale->total_amount, 2) }}</span>
    </div>
    <div class="receipt-row">
        <span>Amount Paid</span>
        <span>₱{{ number_format($sale->amount_paid, 2) }}</span>
    </div>
    <div class="receipt-row">
        <span>Change</span>
        <span>₱{{ number_format($sale->change_amount, 2) }}</span>
    </div>

    <div class="receipt-divider"></div>

    <div class="text-center" style="font-size: 11px; margin-top: 10px;">
        ARIGATOU GOZAIMASU! <br>
        Thank you for shopping with us!
    </div>
</div>

<button class="btn-print" onclick="window.print();">Print Receipt</button>

</body>
</html>
