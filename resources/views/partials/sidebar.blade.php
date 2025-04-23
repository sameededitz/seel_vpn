<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div class="d-flex align-items-center">
            <img src="{{ asset('assets/images/logo-icon.png') }}" class="logo-icon" alt="logo icon">
            <h2 class="logo-text">Seel VPN</h2>
        </div>
        <div class="toggle-icon ms-auto"><i class='bx bx-arrow-back'></i>
        </div>
    </div>
    <!--navigation-->
    <ul class="metismenu" id="menu">
        <li>
            <a href="{{ route('admin.dashboard') }}">
                <div class="parent-icon">
                    <iconify-icon icon="qlementine-icons:home-16" class="flex-shrink-0" width="20"
                        height="20"></iconify-icon>
                </div>
                <div class="menu-title">Dashboard</div>
            </a>
        </li>

        <li class="menu-label">Applications</li>

        <li>
            <a href="{{ route('vps-servers.all') }}">
                <div class="parent-icon">
                    <iconify-icon icon="qlementine-icons:server-16" class="flex-shrink-0" width="20"
                        height="20"></iconify-icon>
                </div>
                <div class="menu-title">VPS Servers</div>
            </a>
        </li>
        <li>
            <a href="{{ route('servers.all') }}">
                <div class="parent-icon">
                    <iconify-icon icon="ic:baseline-vpn-lock" class="flex-shrink-0" width="20"
                        height="20"></iconify-icon>
                </div>
                <div class="menu-title">VPN Servers</div>
            </a>
        </li>
        <li>
            <a href="{{ route('plans.all') }}">
                <div class="parent-icon">
                    <iconify-icon icon="mdi:currency-usd" class="flex-shrink-0" width="20"
                        height="20"></iconify-icon>
                </div>
                <div class="menu-title">Plans</div>
            </a>
        </li>
        <li>
            <a href="{{ route('transactions.all') }}">
                <div class="parent-icon">
                    <iconify-icon icon="hugeicons:transaction" class="flex-shrink-0" width="20"
                        height="20"></iconify-icon>
                </div>
                <div class="menu-title">Transactions</div>
            </a>
        </li>
        <li>
            <a href="{{ route('users.all') }}">
                <div class="parent-icon">
                    <iconify-icon icon="ri:user-line" class="flex-shrink-0" width="20"
                        height="20"></iconify-icon>
                </div>
                <div class="menu-title">Users</div>
            </a>
        </li>

        <li class="menu-label">Panel</li>

        <li>
            <a href="{{ route('admins.all') }}">
                <div class="parent-icon">
                    <iconify-icon icon="ri:admin-line" class="flex-shrink-0" width="20"
                        height="20"></iconify-icon>
                </div>
                <div class="menu-title">Admins</div>
            </a>
        </li>
        <li>
            <a href="{{ route('notifications') }}">
                <div class="parent-icon">
                    <iconify-icon icon="iconamoon:notification" class="flex-shrink-0" width="20"
                        height="20"></iconify-icon>
                </div>
                <div class="menu-title">Notification</div>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.tickets') }}">
                <div class="parent-icon">
                    <iconify-icon icon="bx:support" class="flex-shrink-0" width="20" height="20"></iconify-icon>
                </div>
                <div class="menu-title">Ticket Support</div>
            </a>
        </li>
        <li>
            <a href="javascript:void(0);">
                <div class="parent-icon">
                    <iconify-icon icon="mage:email" class="flex-shrink-0" width="20" height="20"></iconify-icon>
                </div>
                <div class="menu-title">Email Manage</div>
            </a>
        </li>
        <li>
            <a href="javascript:void(0);">
                <div class="parent-icon">
                    <iconify-icon icon="material-symbols-light:feedback-outline" class="flex-shrink-0" width="20"
                        height="20"></iconify-icon>
                </div>
                <div class="menu-title">Feedbacks</div>
            </a>
        </li>
        <li>
            <a href="javascript:void(0);">
                <div class="parent-icon">
                    <iconify-icon icon="material-symbols-light:settings-outline-rounded" class="flex-shrink-0"
                        width="20" height="20"></iconify-icon>
                </div>
                <div class="menu-title">Settings</div>
            </a>
        </li>
        {{-- <li class="menu-label">Others</li>
        <li>
            <a href="https://linktr.ee/sameeddev" target="_blank">
                <div class="parent-icon"><i class="bx bx-support"></i>
                </div>
                <div class="menu-title">Support</div>
            </a>
        </li> --}}
    </ul>
    <!--end navigation-->
</div>
