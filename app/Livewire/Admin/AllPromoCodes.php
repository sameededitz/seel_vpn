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

    public string $type = 'single_use';
    public ?int $maxUses = null;

    public ?PromoCode $selectedPromo = null;

    public function viewPromoDetails($id)
    {
        $this->resetForm();
        $this->selectedPromo = PromoCode::with(['users' => function ($query) {
            $query->withPivot('used_at', 'purchase_id')->latest('promo_code_user.used_at');
        }])->findOrFail($id);
    }

    public function resetForm()
    {
        $this->count = 1;
        $this->discount = null;
        $this->expiresAt = null;
        $this->type = 'single_use'; // Reset type to default
        $this->maxUses = null; // Reset maxUses to null
        $this->selectedPromo = null;
        $this->resetValidation();
        $this->resetPage();
    }

    public function generatePromoCode()
    {
        $this->validate([
            'discount' => 'required|integer|min:1|max:100',
            'count' => 'required|integer|min:1|max:100',
            'expiresAt' => 'required|date|after:now',
            'type' => 'required|in:single_use,multi_use',
            'maxUses' => 'nullable|required_if:type,multi_use|integer|min:1|max:100',
        ]);

        for ($i = 0; $i < $this->count; $i++) {
            PromoCode::create([
                'code' => strtoupper(Str::random(8)),
                'discount_percent' => $this->discount,
                'expires_at' => $this->expiresAt,
                'type' => $this->type,
                'max_uses' => $this->type === 'multi_use' ? $this->maxUses : null,
                'is_active' => true,
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
            ->withCount('users') // Pivot usage count
            ->when($this->search, function ($query) {
                $query->where('code', 'like', '%' . $this->search . '%');
            })
            ->when($this->usedFilter, function ($query) {
                if ($this->usedFilter === 'used') {
                    $query->where('uses_count', '>', 0);
                } elseif ($this->usedFilter === 'unused') {
                    $query->where('uses_count', 0);
                }
            })
            ->paginate($this->perPage);

        /** @disregard @phpstan-ignore-line */
        return view('livewire.admin.all-promo-codes', compact('codes'))
            ->extends('layouts.app')
            ->section('content');
    }
}
