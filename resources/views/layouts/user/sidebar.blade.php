{{-- /**
* Copyright (c) 2025 FPT University
*
* @author Phạm Hoàng Tuấn
* @email phamhoangtuanqn@gmail.com
* @facebook fb.com/phamhoangtuanqn
*/ --}}

<div class="profile-sidebar">
    <div class="sidebar-header">
        <h2 class="sidebar-title">MENU TÀI KHOẢN</h2>
    </div>
    <ul class="sidebar-menu">
        <li class="sidebar-item {{ request()->routeIs('profile.index') ? 'active' : '' }}">
            <a href="{{ route('profile.index') }}" class="sidebar-link">
                <i class="fa-solid fa-user"></i> Thông tin tài khoản
            </a>
        </li>
        @if (config_get('payment.card.active', true))
            <li class="sidebar-item {{ request()->routeIs('profile.deposit-card') ? 'active' : '' }}">
                <a href="{{ route('profile.deposit-card') }}" class="sidebar-link">
                    <i class="fa-solid fa-credit-card"></i> Nạp tiền thẻ cào
                </a>
            </li>
        @endif
        <li class="sidebar-item {{ request()->routeIs('profile.deposit-atm') ? 'active' : '' }}">
            <a href="{{ route('profile.deposit-atm') }}" class="sidebar-link">
                <i class="fa-solid fa-money-bill-transfer"></i> Nạp tiền ATM
            </a>
        </li>
        <li class="sidebar-item {{ request()->routeIs('profile.transaction-history') ? 'active' : '' }}">
            <a href="{{ route('profile.transaction-history') }}" class="sidebar-link">
                <i class="fa-solid fa-chart-line"></i> Biến động số dư
            </a>
        </li>
    </ul>

    <div class="sidebar-header mt-4">
        <h2 class="sidebar-title">MENU GIAO DỊCH</h2>
    </div>
    <ul class="sidebar-menu">
        <li class="sidebar-item {{ request()->routeIs('profile.purchased-accounts') ? 'active' : '' }}">
            <a href="{{ route('profile.purchased-accounts') }}" class="sidebar-link">
                <i class="fa-solid fa-box"></i> Tài khoản đã mua
            </a>
        </li>
    </ul>
</div>
