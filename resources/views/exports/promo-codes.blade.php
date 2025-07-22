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
        @foreach($promoCodes as $code)
            <tr>
                <td>{{ $code->code }}</td>
                <td>{{ $code->discount_percent }}%</td>
                <td>{{ $code->type }}</td>
                <td>{{ $code->max_uses ?? 'Unlimited' }}</td>
                <td>{{ $code->uses_count }}</td>
                <td>{{ optional($code->expires_at)->toDateString() ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
