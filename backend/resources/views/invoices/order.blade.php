<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .info-box {
            width: 48%;
        }
        .info-box h3 {
            margin-top: 0;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .totals {
            float: right;
            width: 300px;
            margin-top: 20px;
        }
        .totals table {
            margin-bottom: 0;
        }
        .totals td {
            text-align: right;
        }
        .totals .total-row {
            font-weight: bold;
            font-size: 14px;
            border-top: 2px solid #333;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVOICE</h1>
        <p>Invoice #{{ $invoice_number }} | Order #{{ $order_number }}</p>
    </div>

    <div class="invoice-info">
        <div class="info-box">
            <h3>Bill To:</h3>
            <p><strong>{{ $customer['name'] }}</strong></p>
            <p>{{ $customer['email'] }}</p>
            @if($customer['phone'])
            <p>{{ $customer['phone'] }}</p>
            @endif
            <p style="white-space: pre-line;">{{ $customer['address'] }}</p>
        </div>
        <div class="info-box">
            <h3>Invoice Details:</h3>
            <p><strong>Invoice Date:</strong> {{ $invoice_date }}</p>
            <p><strong>Order Date:</strong> {{ $order_date }}</p>
            <p><strong>Payment Method:</strong> {{ $payment_method }}</p>
            <p><strong>Payment Status:</strong> {{ $payment_status }}</p>
            <p><strong>Order Status:</strong> {{ $status }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>SKU</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item['name'] }}</td>
                <td>{{ $item['sku'] }}</td>
                <td>{{ $item['quantity'] }}</td>
                <td>${{ number_format($item['price'], 2) }}</td>
                <td>${{ number_format($item['subtotal'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td>Subtotal:</td>
                <td>${{ number_format($subtotal, 2) }}</td>
            </tr>
            @if($discount_amount > 0)
            <tr>
                <td>Discount @if($coupon_code)({{ $coupon_code }})@endif:</td>
                <td>-${{ number_format($discount_amount, 2) }}</td>
            </tr>
            @endif
            @if($shipping_cost > 0)
            <tr>
                <td>Shipping ({{ $shipping_method }}):</td>
                <td>${{ number_format($shipping_cost, 2) }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td>Total:</td>
                <td>${{ number_format($total, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>This is a computer-generated invoice and does not require a signature.</p>
    </div>
</body>
</html>
