@section('title', 'All Promo Codes')
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
                    <li class="breadcrumb-item active" aria-current="page">Promo Codes</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">All Promo Codes</h3>
                    <button type="button" class="btn btn-light btn-outline-primary px-3 radius-30"
                        data-bs-toggle="modal" data-bs-target="#promoCodeModel" wire:click="resetForm">
                        Create Promo Code
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
                            <button type="button" wire:click="$js.confirmDeleteAllUnused()"
                                class="btn btn-outline-danger d-flex align-items-center justify-content-center">
                                <Iconify-icon icon="mingcute:delete-2-line" width="20"
                                    height="20"></Iconify-icon>
                            </button>

                            <div class="dropdown">
                                <button class="btn btn-outline-success dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown">
                                    Export
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ route('export.unused.codes', ['format' => 'csv', 'search' => $search, 'type' => $typeFilter, 'usage' => $usedFilter]) }}">
                                            Export as CSV
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ route('export.unused.codes', ['format' => 'xlsx', 'search' => $search, 'type' => $typeFilter, 'usage' => $usedFilter]) }}">
                                            Export as Excel
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ route('export.unused.codes.pdf', ['format' => 'pdf', 'search' => $search, 'type' => $typeFilter, 'usage' => $usedFilter]) }}">
                                            Export as PDF
                                        </a>
                                    </li>
                                </ul>
                            </div>

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
                            <div class="dropdown">
                                <button class="btn btn-light btn-rounded d-flex" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <iconify-icon icon="line-md:filter" width="24" height="24"></iconify-icon>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <div class="dropdown-header text-start d-flex justify-content-between mb-3">
                                            <h6 class="text-uppercase mb-0">Filters</h6>
                                            <h6 class="text-danger mb-0" wire:click="resetFilters"
                                                style="cursor: pointer;">Reset</h6>
                                        </div>
                                        <div class="dropdown-item">
                                            <label for="usedFilter" class="mb-1">Usage:</label>
                                            <select class="form-select w-100" id="usedFilter"
                                                wire:model.live="usedFilter">
                                                <option value="">All</option>
                                                <option value="used">Used</option>
                                                <option value="unused">Unused</option>
                                            </select>
                                        </div>
                                        <div class="dropdown-item">
                                            <label for="typeFilter" class="mb-1">Type:</label>
                                            <select class="form-select w-100" id="typeFilter"
                                                wire:model.live="typeFilter">
                                                <option value="">All</option>
                                                <option value="single_use">Single Use</option>
                                                <option value="multi_use">Multi Use</option>
                                            </select>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <table class="table table-responsive">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Code</th>
                                <th>Discount (%)</th>
                                <th>Status</th>
                                <th>Type</th>
                                <th>Used By</th>
                                <th>Used At</th>
                                <th>Expires At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($codes as $code)
                                <tr>
                                    <td>{{ $code->id }}</td>
                                    <td>{{ $code->code }}</td>
                                    <td>{{ $code->discount_percent ?? '-' }}%</td>
                                    <td>
                                        @if ($code->type === 'single_use')
                                            @if ($code->uses_count > 0)
                                                <span class="text-green-600">Used</span>
                                            @else
                                                <span class="text-gray-600">Unused</span>
                                            @endif
                                        @elseif ($code->type === 'multi_use')
                                            @if ($code->uses_count >= $code->max_uses)
                                                <span class="text-red-600">Expired</span>
                                            @elseif ($code->uses_count > 0)
                                                <span class="text-blue-600">Partially Used</span>
                                            @else
                                                <span class="text-gray-600">Unused</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if ($code->type === 'single_use')
                                            <span class="badge bg-primary">Single Use</span>
                                        @else
                                            <span class="badge bg-secondary">Multi Use
                                                (Max:{{ $code->max_uses }})
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($code->type === 'multi_use')
                                            <span>{{ $code->users_count }} user(s)</span>
                                        @elseif ($code->users_count > 0)
                                            @php $user = $code->users()->latest('promo_code_user.used_at')->first(); @endphp
                                            <a href="{{ route('user.manage', $user->id) }}" class="text-info">
                                                {{ $user->email }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $usedAt = $code->users()->latest('promo_code_user.used_at')->first()?->pivot
                                                ->used_at;
                                        @endphp
                                        {{ $usedAt ? \Carbon\Carbon::parse($usedAt)->diffForHumans() : '-' }}
                                    </td>
                                    <td>{{ $code->expires_at?->toFormattedDateString() ?? '-' }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <button type="button" wire:click="viewPromoDetails({{ $code->id }})"
                                                class="btn btn-outline-info d-flex align-items-center justify-content-center"
                                                data-bs-toggle="modal" data-bs-target="#promoDetailsModal">
                                                <Iconify-icon icon="mdi:eye-outline" width="20"
                                                    height="20"></Iconify-icon>
                                            </button>

                                            <button type="button"
                                                wire:click="$js.confirmDelete({{ $code->id }})"
                                                class="btn btn-outline-danger d-flex align-items-center justify-content-center">
                                                <Iconify-icon icon="mingcute:delete-2-line" width="20"
                                                    height="20"></Iconify-icon>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No Promo Codes found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $codes->links('components.pagination', data: ['scrollTo' => false]) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="promoDetailsModal" tabindex="-1" wire:ignore.self
        aria-labelledby="promoDetailsModal" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" wire:loading.remove>
                <div class="modal-header">
                    <h5 class="modal-title">Promo Code Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        wire:click="resetForm"></button>
                </div>

                <div class="modal-body">
                    @if ($selectedPromo)
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Code:</strong> {{ $selectedPromo->code }}</li>
                            <li class="list-group-item"><strong>Type:</strong>
                                {{ ucfirst(str_replace('_', ' ', $selectedPromo->type)) }}</li>
                            <li class="list-group-item"><strong>Discount:</strong>
                                {{ $selectedPromo->discount_percent }}%</li>
                            <li class="list-group-item"><strong>Max Uses:</strong>
                                {{ $selectedPromo->max_uses ?? '-' }}</li>
                            <li class="list-group-item"><strong>Expires At:</strong>
                                {{ $selectedPromo->expires_at?->toFormattedDateString() ?? '-' }}</li>
                            <li class="list-group-item"><strong>Used By:</strong> {{ $selectedPromo->users->count() }}
                                user(s)</li>
                        </ul>

                        <hr>

                        @if ($selectedPromo->users->count())
                            <h6>Users who used this code:</h6>
                            <ul class="list-group">
                                @foreach ($selectedPromo->users as $user)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <a href="{{ route('user.manage', $user->slug) }}"
                                                class="text-primary">{{ $user->email }}</a>
                                            <small class="d-block">Used at:
                                                {{ \Carbon\Carbon::parse($user->pivot->used_at)->toDayDateTimeString() }}</small>
                                        </div>
                                        <span class="badge bg-primary">Purchase ID:
                                            {{ $user->pivot->purchase_id }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="mt-2 mb-0">No user has used this promo code yet.</p>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="promoCodeModel" tabindex="-1" wire:ignore.self aria-labelledby="promoCodeModel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Generate Promo Code(s)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="resetForm"
                        aria-label="Close"></button>
                </div>

                <form class="row g-3" wire:submit.prevent="generatePromoCode">
                    <div class="modal-body">
                        <div class="col-12 mb-2">
                            <label for="discount" class="form-label">Discount (%)</label>
                            <input type="number" wire:model="discount" min="1" max="100"
                                class="form-control" required />
                            @error('discount')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-12 mb-2">
                            <label for="count" class="form-label">Number of Codes</label>
                            <input type="number" wire:model="count" min="1" max="100"
                                class="form-control" required />
                            @error('count')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-12 mb-2">
                            <label for="type" class="form-label">Promo Code Type</label>
                            <select wire:model.live="type" class="form-select w-100">
                                <option value="single_use">Single Use</option>
                                <option value="multi_use">Multi Use</option>
                            </select>
                            @error('type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        @if ($type === 'multi_use')
                            <div class="col-12 mb-2">
                                <label for="maxUses" class="form-label">Max Uses</label>
                                <input type="number" wire:model="maxUses"min="1" max="100"
                                    class="form-control" />
                                @error('maxUses')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif

                        <div class="col-12">
                            <label for="expiresAt" class="form-label">Expiry Date</label>
                            <input type="date" wire:model="expiresAt" class="form-control" required />
                            @error('expiresAt')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                            wire:click="resetForm">Cancel</button>
                        <button type="submit" class="btn btn-success">Generate</button>
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
                    $wire.deletePromoCode(id);
                }
            });
        });

        $js('confirmDeleteAllUnused', () => {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This will delete all unused promo codes!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete all unused!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.deleteAllUnused();
                }
            });
        });

        $wire.on('closeModel', (event) => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('promoCodeModel'));
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
