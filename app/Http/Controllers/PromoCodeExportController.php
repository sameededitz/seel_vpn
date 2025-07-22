<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PromoCode;

class PromoCodeExportController extends Controller
{
    public function unused(Request $request)
    {
        $filename = 'promo_codes_export_' . now()->format('Y_m_d_H_i_s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($request) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Code', 'Discount (%)', 'Type', 'Max Uses', 'Uses Count', 'Expires At']);

            $hasFilters = $request->filled('search') || $request->filled('type') || $request->filled('usage');

            $query = PromoCode::query();

            if ($hasFilters) {
                $query
                    ->when($request->filled('search'), fn($q) => $q->where('code', 'like', '%' . $request->search . '%'))
                    ->when($request->filled('type'), fn($q) => $q->where('type', $request->type))
                    ->when($request->filled('usage'), function ($q) use ($request) {
                        if ($request->usage === 'used') {
                            $q->where('uses_count', '>', 0);
                        } elseif ($request->usage === 'unused') {
                            $q->where('uses_count', 0);
                        }
                    });
            } else {
                // Default to unused
                $query->where('uses_count', 0);
            }

            $query->orderBy('created_at', 'desc')->get()
                ->each(function ($code) use ($handle) {
                    fputcsv($handle, [
                        $code->code,
                        $code->discount_percent,
                        $code->type,
                        $code->max_uses ?? 'Unlimited',
                        $code->uses_count,
                        optional($code->expires_at)->toDateString() ?? '-',
                    ]);
                });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
