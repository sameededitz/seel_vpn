<?php

namespace App\Exports;

use App\Models\PromoCode;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PromoCodesExport implements FromView
{
    public function __construct(
        protected ?string $search = null,
        protected ?string $type = null,
        protected ?string $usage = null
    ) {}

    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): view
    {
        $query = PromoCode::query();

        if ($this->search || $this->type || $this->usage) {
            $query->when($this->search, fn($q) => $q->where('code', 'like', '%' . $this->search . '%'))
                ->when($this->type, fn($q) => $q->where('type', $this->type))
                ->when($this->usage, function ($q) {
                    if ($this->usage === 'used') {
                        $q->where('uses_count', '>', 0);
                    } elseif ($this->usage === 'unused') {
                        $q->where('uses_count', 0);
                    }
                });
        } else {
            $query->where('uses_count', 0); // default to unused
        }

        return view('exports.promo-codes', [
            'promoCodes' => $query->get(),
        ]);
    }
}
