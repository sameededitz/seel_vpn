<?php

namespace App\Livewire\Admin;
use App\Models\Notification;
use Livewire\Component;
use Livewire\WithPagination;

class AllNotification extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 5;
    public $isEdit = false;


    public $title;
    public $body;
    public $notificationId;

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'body' => 'nullable|string|max:1000',
        ];
    }
    public function resetForm()
    {
        $this->reset([
            'notificationId',
            'title',
            'body',
        ]);
        $this->isEdit = false;
        $this->resetValidation();
    }

    public function editNotification($notificationId)
    {
        $this->resetForm();
        $this->isEdit = true;

        $notifications =Notification::findOrFail($notificationId);
        $this->notificationId = $notifications->id;
        $this->title = $notifications->title;
        $this->body = $notifications->body;
    }

    public function saveNotification()
    {
        $this->validate();

        if ($this->isEdit) {
            $notifications = Notification::findOrFail($this->notificationId);
            $notifications->update([
                'title' => $this->tile,
                'body' => $this->body,
            ]);
            $message = 'Notification updated successfully.';
        } else {
            Notification::create([
                'title' => $this->title,
                'body' => $this->body,
            ]);
            $message = 'Notification created successfully.';
        }

        $this->dispatch('closeModel');
        $this->dispatch('sweetAlert', title: 'Success!', message: $message, type: 'success');
        $this->resetPage();
        $this->resetForm();
    }

    public function deleteNotification($notificationId)
    {
        $notifications = Notification::findOrFail($notificationId);
        $notifications->delete();

        $this->dispatch('sweetAlert', title: 'Deleted!', message: 'Notification has been deleted successfully.', type: 'success');
    }
    
    public function render()
    {
            $notifications = Notification::query()
                ->when($this->search, fn($query) => $query->where('name', 'like', '%' . $this->search . '%'))
                ->latest()
                ->paginate($this->perPage);
        /** @disregard @phpstan-ignore-line */
        return view('livewire.admin.notification', compact('notifications'))
        ->extends('layouts.app')
        ->section('content');
    }
}
