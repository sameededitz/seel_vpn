@section('title', 'Notifications')
<div>
    @if (session('message'))
        <x-alert type="info" :message="session('message')" />
    @endif
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Home</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Notifications</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">All Notifications</h3>
                    <button type="button" class="btn btn-light btn-outline-primary px-3 radius-30"
                        data-bs-toggle="modal" data-bs-target="#notificationModel" wire:click="resetForm">
                        Create Notification
                    </button>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3 flex-wrap gap-3 align-items-center">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <select class="form-select" wire:model.live="perPage">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="20">20</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search..."
                                    wire:model.live.500ms="search">
                                <span class="input-group-text" id="basic-addon1">
                                    <Iconify-icon icon="material-symbols-light:search" width="20"
                                        height="20"></Iconify-icon>
                                </span>
                            </div>
                        </div>
                    </div>
                    <table class="table table-responsive">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Message</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($notifications as $notification)
                                <tr>
                                    <td>{{ $notification->id }}</td>
                                    <td>{{ $notification->title }}</td>
                                    <td>{{ $notification->message }}</td>
                                    <td>{{ $notification->created_at->toFormattedDateString() }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <button type="button" wire:click="editNotification({{ $notification->id }})"
                                                data-bs-toggle="modal" data-bs-target="#notificationModel"
                                                class="btn btn-outline-warning d-flex align-items-center justify-content-center">
                                                <iconify-icon icon="material-symbols:edit" width="20"
                                                    height="20"></iconify-icon>
                                            </button>
                                            <button type="button" wire:click="$js.confirmDelete({{ $notification->id }})"
                                                class="btn btn-outline-danger d-flex align-items-center justify-content-center">
                                                <Iconify-icon icon="mingcute:delete-2-line" width="20"
                                                    height="20"></Iconify-icon>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No notifications found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $notifications->links('components.pagination', data: ['scrollTo' => false]) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="notificationModel" tabindex="-1" wire:ignore.self aria-labelledby="notificationModel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        {{ $isEdit ? 'Edit Notification' : 'Add New Notification' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="resetForm"
                        aria-label="Close"></button>
                </div>
                <form class="row g-2" wire:submit.prevent="saveNotification">
                    <div class="modal-body">
                        <div class="col-12">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" placeholder="Title"
                                wire:model.defer="title">
                            @error('title')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" placeholder="Message"
                                wire:model.defer="message"></textarea>
                            @error('message')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button"
                            class="btn btn-outline-info d-flex align-items-center justify-content-center"
                            wire:click="resetForm" data-bs-dismiss="modal">Close</button>
                        <button type="submit"
                            class="btn btn-outline-success d-flex align-items-center justify-content-center">
                            {{ $isEdit ? 'Update' : 'Save' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@script
    <script>
        $js('confirmDelete', (id) => {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You won\'t be able to revert this!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.deleteNotification(id);
                }
            });
        });

        $wire.on('closeModel', (event) => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('notificationModel'));
            modal.hide();
        });

        $wire.on('sweetAlert', (event) => {
            Swal.fire({
                title: event.title,
                text: event.message,
                icon: event.type,
                timer: 2000,
                showConfirmButton: false
            });
        });
    </script>
@endscript
