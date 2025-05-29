<?php

namespace App\Livewire\Admin;

use App\Models\Plan;
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
    #[Url('plan')]
    public string $planFilter = '';
    public int $perPage = 5;

    public int $count = 1;
    public ?int $selectedPlan = null;
    public ?string $expiresAt = null;

    public function resetForm()
    {
        $this->count = 1;
        $this->selectedPlan = null;
        $this->expiresAt = null;
        $this->resetValidation();
        $this->resetPage();
    }

    public function generatePromoCode()
    {
        $this->validate([
            'selectedPlan' => 'required|exists:plans,id',
            'count' => 'required|integer|min:1|max:100',
            'expiresAt' => 'required|date|after:now',
        ]);

        $plan = Plan::findOrFail($this->selectedPlan);

        for ($i = 0; $i < $this->count; $i++) {
            PromoCode::create([
                'code' => strtoupper(Str::random(8)),
                'plan_id' => $plan->id,
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
    public function updatedPlanFilter()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->usedFilter = '';
        $this->planFilter = '';
        $this->resetPage();
    }

    public function render()
    {
        $plans = Plan::all('name', 'id', 'slug');

        $codes = PromoCode::query()
            ->with(['plan', 'user'])
            ->when($this->search, function ($query) {
                $query->where('code', 'like', '%' . $this->search . '%')
                    ->orWhereHas('plan', function ($q) {
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
            ->when($this->planFilter, function ($query) {
                $query->whereHas('plan', function ($q) {
                    $q->where('slug', $this->planFilter);
                });
            })
            ->paginate($this->perPage);

        /** @disregard @phpstan-ignore-line */
        return view('livewire.admin.all-promo-codes', compact('codes', 'plans'))
            ->extends('layouts.app')
            ->section('content');
    }
}
