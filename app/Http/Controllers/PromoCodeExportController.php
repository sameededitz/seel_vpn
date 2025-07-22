<?php

namespace App\Http\Controllers;

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
}
