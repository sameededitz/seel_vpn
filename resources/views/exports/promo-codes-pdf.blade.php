<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Promo Codes Export</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        h2 {
            text-align: center;
            margin-top: 20px;
        }

        .small {
            font-size: 10px;
            color: #777;
        }
    </style>
</head>

<body>

    <h2>Promo Codes Export</h2>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Discount (%)</th>
                <th>Type</th>
                <th>Max Uses</th>
                <th>Uses Count</th>
                <th>Expires At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($promoCodes as $index => $code)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $code->code }}</td>
                    <td>{{ $code->discount_percent }}%</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $code->type)) }}</td>
                    <td>
                        @if ($code->type === 'single_use')
                            1
                        @else
                            {{ $code->max_uses ?? 'Unlimited' }}
                        @endif
                    </td>
                    <td>{{ $code->uses_count }}</td>
                    <td>{{ optional($code->expires_at)->toDateString() ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
