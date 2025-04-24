<?php

use App\Livewire\Admin\AllPlans;
use App\Livewire\Admin\AllUsers;
use App\Livewire\Admin\AllAdmins;
use App\Livewire\Admin\ServerAdd;
use App\Livewire\Admin\AllServers;
use App\Livewire\Admin\AllTickets;
use App\Livewire\Admin\ServerEdit;
use App\Livewire\Admin\VpsManager;
use App\Livewire\Admin\AllPurchases;
use App\Livewire\Admin\SubServerAdd;
use App\Livewire\Admin\AllSubServers;
use App\Livewire\Admin\AllVpsServers;
use App\Livewire\Admin\SubServerEdit;
use App\Livewire\Admin\VpsServersAdd;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Livewire\Admin\AllNotification;
use App\Livewire\Admin\Setting;
use App\Livewire\Admin\TicketDetails;

Route::group(['middleware' => ['auth']], function () {
    Route::get('/admin', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/vps-servers', AllVpsServers::class)->name('vps-servers.all');
    Route::get('/vps-servers/create', VpsServersAdd::class)->name('vps-servers.add');
    Route::get('/vps-servers/{vpsServer}/manage', VpsManager::class)->name('vps-servers.manage');

    Route::get('/servers', AllServers::class)->name('servers.all');
    Route::get('/servers/create', ServerAdd::class)->name('servers.add');
    Route::get('/servers/{server}/edit', ServerEdit::class)->name('servers.edit');

    Route::get('/server/{server}/sub-servers', AllSubServers::class)->name('all.sub-servers');
    Route::get('/server/{server}/sub-servers/create', SubServerAdd::class)->name('sub-server.add');
    Route::get('/server/{server}/sub-servers/{subServer}/edit', SubServerEdit::class)->name('sub-server.edit');

    Route::get('/plans', AllPlans::class)->name('plans.all');

    Route::get('/notifications', AllNotification::class)->name('notifications');

    Route::get('/transactions', AllPurchases::class)->name('transactions.all');

    Route::get('/users', AllUsers::class)->name('users.all');

    Route::get('/setting', Setting::class)->name('setting');

    Route::get('/admin-accounts', AllAdmins::class)->name('admins.all');

    Route::get('/tickets', AllTickets::class)->name('admin.tickets');
    Route::get('/tickets/{ticketId}', TicketDetails::class)->name('admin.ticket.details');
});
