<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\PromoCode;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class AllPromoCodes extends Component
{
    use WithPagination;

    public string $search = '';

    #[Url('usage')]
    public string $usedFilter = '';
    public int $perPage = 5;

    public int $count = 1;
    public ?int $discount = null;
    public ?string $expiresAt = null;

    public function resetForm()
    {
        $this->count = 1;
        $this->discount = null;
        $this->expiresAt = null;
        $this->resetValidation();
        $this->resetPage();
    }

    public function generatePromoCode()
    {
        $this->validate([
            'discount' => 'required|integer|min:1|max:100',
            'count' => 'required|integer|min:1|max:100',
            'expiresAt' => 'required|date|after:now',
        ]);

        for ($i = 0; $i < $this->count; $i++) {
            PromoCode::create([
                'code' => strtoupper(Str::random(8)),
                'discount_percent' => $this->discount,
                'is_active' => true,
                'expires_at' => $this->expiresAt,
            ]);
        }

        $this->dispatch('closeModel');
        $this->dispatch('sweetAlert', title: 'Success', message: 'Promo codes generated successfully!', type: 'success');
        $this->resetForm();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function updatedUsedFilter()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->usedFilter = '';
        $this->resetPage();
    }

    public function deletePromoCode(PromoCode $promoCode)
    {
        $promoCode->delete();
        $this->dispatch('sweetAlert', title: 'Success', message: 'Promo code deleted successfully!', type: 'success');
    }

    public function deleteAllUnused()
    {
        PromoCode::unused()->delete();
        $this->dispatch('sweetAlert', title: 'Success', message: 'All unused promo codes deleted successfully!', type: 'success');
        $this->resetPage();
    }

    public function render()
    {
        $codes = PromoCode::query()
            ->with(['purchase', 'user'])
            ->when($this->search, function ($query) {
                $query->where('code', 'like', '%' . $this->search . '%');
            })
            ->when($this->usedFilter, function ($query) {
                if ($this->usedFilter === 'used') {
                    $query->used();
                } elseif ($this->usedFilter === 'unused') {
                    $query->unused();
                }
            })
            ->paginate($this->perPage);

        /** @disregard @phpstan-ignore-line */
        return view('livewire.admin.all-promo-codes', compact('codes'))
            ->extends('layouts.app')
            ->section('content');
    }
}
