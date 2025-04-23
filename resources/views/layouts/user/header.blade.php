<!-- Nav -->
<nav class="nav">
    <div class="container row">
        <a href="/" style="display: flex; align-items: center">
            <img src="{{ config_get('site_logo') }}" alt="{{ config_get('site_name') }}" class="nav__logo" />
        </a>
        <div class="nav__menu">
            <a href="/" class="text menu__item {{ request()->is('/') ? 'active' : '' }}">Trang chủ</a>
            <a href="{{ route('profile.deposit-card') }}"
                class="text menu__item {{ request()->routeIs('profile.deposit-card') ? 'active' : '' }}">Nạp tiền</a>
            <a href="{{ route('service.show-all') }}"
                class="text menu__item {{ request()->routeIs('service.*') ? 'active' : '' }}">Dịch vụ</a>

            <a href="{{ route('category.show-all') }}"
                class="text menu__item {{ request()->routeIs('category.*') ? 'active' : '' }}">Nick game</a>

            {{-- <a href="{{ route('random.show-all') }}"
                class="text menu__item {{ request()->routeIs('random.*') ? 'active' : '' }}">Random</a> --}}

            @if (Auth::check() && Auth()->user()->role == 'admin')
                <a href="{{ route('admin.index') }}" target="_blank" class="text menu__item">ADMIN PANEL</a>
            @endif
        </div>
        <button class="menu-toggle" aria-label="Toggle Menu">
            <i class="fas fa-bars"></i>
        </button>
        <div class="nav__action">
            @guest
                <a href="{{ route('login') }}" class="text action__link"><i class="fa-solid fa-user"></i> Đăng nhập</a>
                <a href="{{ route('register') }}" class="text action__link action__link--primary"><i
                        class="fa-solid fa-key"></i> Đăng ký</a>
            @else
                <a href="{{ route('profile.index') }}" class="text"><i class="fa-solid fa-user"></i>
                    {{ Auth::user()->username }}
                    -
                    <span class="balance-value">{{ number_format(Auth::user()->balance) }}đ</span></a>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="text action__link action__link--primary">
                        <i class="fa-solid fa-sign-out-alt"></i> Đăng xuất
                    </button>
                </form>
            @endguest
        </div>
    </div>
</nav>
