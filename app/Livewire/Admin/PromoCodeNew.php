<?php

namespace App\Livewire\Admin;

use App\Models\Plan;
use Livewire\Component;
use App\Models\NewCodes;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class PromoCodeNew extends Component
{
    use WithPagination;

    public string $search = '';

    #[Url('usage')]
    public string $usedFilter = '';
    #[Url('plan')]
    public string $planFilter = '';
    public int $perPage = 5;

    public int $count = 1;
    public int $percentage = 1;
    

    public function resetForm()
    {
        $this->count = 1;
        $this->percentage = 1;
        $this->resetValidation();
        $this->resetPage();
    }

    public function generatePromoCode()
    {
        $this->validate([
            'count' => 'required|integer|min:1|max:100',
            'percentage' => 'required|integer|min:1|max:100',
        ]);

        for ($i = 0; $i < $this->count; $i++) {
            NewCodes::create([
                'code' => strtoupper(Str::random(8)),
                'percentage' => $this->percentage,
                'is_active' => true,
                // 'expires_at' => $this->expiresAt,
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
    public function updatedPlanFilter()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->usedFilter = '';
        $this->resetPage();
    }

    public function render()
    {

        $codes = NewCodes::query()
            ->with(['user'])
            ->when($this->search, function ($query) {
                $query->where('code', 'like', '%' . $this->search . '%')
                    ->orWhereHas(function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
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
        return view('livewire.admin.promo-code-new', compact('codes'))
            ->extends('layouts.app')
            ->section('content');
    }
}
