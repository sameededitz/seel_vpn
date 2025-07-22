<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PromoCode;
use Illuminate\Http\Request;
use App\Exports\PromoCodesExport;
use Maatwebsite\Excel\Facades\Excel;

class PromoCodeExportController extends Controller
{
    public function export(Request $request)
    {
        $format = $request->get('format', 'xlsx'); // default

        $formats = [
            'xlsx' => \Maatwebsite\Excel\Excel::XLSX,
            'csv'  => \Maatwebsite\Excel\Excel::CSV,
            'pdf'  => \Maatwebsite\Excel\Excel::DOMPDF,
        ];

        $extension = array_key_exists($format, $formats) ? $format : 'xlsx';

        return Excel::download(
            new PromoCodesExport(
                search: $request->get('search'),
                type: $request->get('type'),
                usage: $request->get('usage'),
            ),
            'promo_codes_export_' . now()->format('Y_m_d_H_i_s') . '.' . $extension,
            $formats[$extension]
        );
    }

    public function exportPdf(Request $request)
    {
        $query = PromoCode::query();

        if ($request->filled('search') || $request->filled('type') || $request->filled('usage')) {
            $query
                ->when($request->search, fn ($q) => $q->where('code', 'like', "%{$request->search}%"))
                ->when($request->type, fn ($q) => $q->where('type', $request->type))
                ->when($request->usage, function ($q) use ($request) {
                    if ($request->usage === 'used') {
                        $q->where('uses_count', '>', 0);
                    } elseif ($request->usage === 'unused') {
                        $q->where('uses_count', 0);
                    }
                });
        } else {
            $query->where('uses_count', 0);
        }

        $promoCodes = $query->get();

        $pdf = PDF::loadView('exports.promo-codes-pdf', compact('promoCodes'))
                  ->setPaper('a4', 'landscape'); // You can adjust size

        return $pdf->download('promo_codes_' . now()->format('Ymd_His') . '.pdf');
    }
}
