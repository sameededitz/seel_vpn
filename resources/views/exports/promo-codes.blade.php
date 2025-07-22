<style>
    table {
        width: 100%;
        border-collapse: collapse;
        font-family: sans-serif;
        font-size: 12px;
    }

    th,
    td {
        border: 1px solid #ccc;
        padding: 6px;
        text-align: left;
    }

    thead {
        background-color: #f2f2f2;
    }

    h1 {
        text-align: center;
        margin-bottom: 20px;
    }
</style>

<h1>Promo Codes Export</h1>

<table>
    <thead>
        <tr>
            <th>Code</th>
            <th>Discount (%)</th>
            <th>Type</th>
            <th>Max Uses</th>
            <th>Uses Count</th>
            <th>Expires At</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($promoCodes as $code)
            <tr>
                <td>{{ $code->code }}</td>
                <td>{{ $code->discount_percent }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $code->type)) }}</td>
                <td>{{ $code->max_uses ?? 'Unlimited' }}</td>
                <td>{{ $code->uses_count }}</td>
                <td>{{ optional($code->expires_at)->toFormattedDateString() ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
