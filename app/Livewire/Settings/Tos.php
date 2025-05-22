<?php

namespace App\Livewire\Settings;

use App\Models\Option;
use Livewire\Component;

class Tos extends Component
{
    public $tos;
    public $privacy_policy;
    public $about_us;

    protected function rules()
    {
        return [
            'tos' => 'required',
            'privacy_policy' => 'required',
            'about_us' => 'required',
        ];
    }

    public function mount()
    {
        $this->tos = Option::where('key', 'tos')->first()->value ?? '';
        $this->privacy_policy = Option::where('key', 'privacy_policy')->first()->value ?? '';
        $this->about_us = Option::where('key', 'about_us')->first()->value ?? '';
    }

    public function save()
    {
        $this->validate();

        Option::updateOrCreate(
            ['key' => 'tos'],
            ['value' => $this->tos]
        );
        Option::updateOrCreate(
            ['key' => 'privacy_policy'],
            ['value' => $this->privacy_policy]
        );
        Option::updateOrCreate(
            ['key' => 'about_us'],
            ['value' => $this->about_us]
        );

        $this->reset(['tos', 'privacy_policy', 'about_us']);

        $this->dispatch('sweetAlert', title: 'Success', message: 'Settings updated successfully.', type: 'success');
    }

    public function render()
    {
        /** @disregard @phpstan-ignore-line */
        return view('livewire.settings.tos')
            ->extends('layouts.app')
            ->section('content');
    }
}
