@extends('layouts.user.app')

@section('title', $category->name)

@section('content')
    <!-- Hero Section -->
    <x-hero-header title="{{ $category->name }}" description="{{ $category->description }}"/>

    <!-- Account List Section -->
    <section class="account-section">
        <div class="container">
            <!-- Filter Bar -->
            <form action="" method="GET" class="account-filter">
                <div class="account-filter__row">
                    <div class="account-filter__group">
                        <label for="code" class="account-filter__label">Mã Số:</label>
                        <input type="text" id="code" name="code" class="account-filter__input"
                               placeholder="Nhập Mã Số" value="{{ request('code') }}">
                    </div>

                    <div class="account-filter__group">
                        <label for="price_range" class="account-filter__label">Giá:</label>
                        <select id="price_range" name="price_range"
                                class="account-filter__input account-filter__input--select">
                            <option value="">Tất cả</option>
                            <option value="0-50000" {{ request('price_range') == '0-50000' ? 'selected' : '' }}>Dưới 50K
                            </option>
                            <option value="50000-200000" {{ request('price_range') == '50000-200000' ? 'selected' : '' }}>
                                50K
                                - 200K
                            </option>
                            <option value="200000-500000" {{ request('price_range') == '200000-500000' ? 'selected' : '' }}>
                                200K - 500K
                            </option>
                            <option value="500000-1000000"
                                    {{ request('price_range') == '500000-1000000' ? 'selected' : '' }}>
                                500K - 1M
                            </option>
                            <option value="1000000" {{ request('price_range') == '1000000' ? 'selected' : '' }}>Trên 1M
                            </option>
                        </select>
                    </div>

                    <div class="account-filter__group">
                        <label for="status" class="account-filter__label">Trạng thái:</label>
                        <select id="status" name="status" class="account-filter__input account-filter__input--select">
                            <option value="">Trạng Thái</option>
                            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Chưa bán
                            </option>
                            <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Đã bán</option>
                        </select>
                    </div>
                </div>

                <div class="account-filter__row">
                    <div class="account-filter__group">
                        <label for="registration" class="account-filter__label">Đăng ký:</label>
                        <select id="registration" name="registration"
                                class="account-filter__input account-filter__input--select">
                            <option value="">Tất cả</option>
                            <option value="TTT" {{ request('registration') == 'TTT' ? 'selected' : '' }}>Trắng Thông tin
                            </option>
                        </select>
                    </div>
                </div>

                <div class="account-filter__actions">
                    <button type="submit" class="account-filter__button account-filter__button--primary">
                        <i class="fa-solid fa-filter"></i> TÌM KIẾM
                    </button>
                    <a href="{{ request()->url() }}" class="account-filter__button account-filter__button--outline">
                        <i class="fa-solid fa-rotate"></i> ĐẶT LẠI
                    </a>
                </div>
            </form>

            <!-- Account Grid -->
            <div class="account-grid">
                @forelse($accounts as $account)
                    <div class="account-card">
                        <div class="account-media">
                            @php
                                $image = $account->thumb;
                                $image['url_image'] = Storage::exists($image['url_image']) ? Storage::url($image['url_image']) : $image['url_image'];
                            @endphp
                            <a href="{{ route('account.show', ['id' => $account->id]) }}">
                                <img src="{{ $image['url_image'] }}" alt="Account Preview" class="account-img">
                            </a>
                            <div class="account-code">Mã số: {{ $account->id }}</div>
                            <div class="account-price-top">ATM/VÍ ĐIỆN TỬ: {{ number_format($account->price) }}VND
                            </div>
                        </div>

                        <div class="account-info">
                            <div class="account-row">
                                <div class="info-item__custom">
                                    <span><i class="fa-solid fa-circle-check"></i></span>
                                    <span class="info-value">{{ $account->registration_type === 'TTT' ? 'Trắng thông tin' : $account->registration_type }}</span>
                                </div>
                            </div>
                        </div>

                        @if($note = $account->note)
                            <div class="account-description px-2">
                                <span class="info-value">{{ $note }}</span>
                            </div>
                        @endif

                        <div class="account-actions">
                            <div class="action-btn ">
                                <i class="fas fa-credit-card"></i>
                                {{ number_format($account->price) }}
                                Đ
                            </div>
                            <a href="{{ route('account.show', ['id' => $account->id]) }}"
                               class="action-btn action-btn--detail">XEM CHI TIẾT</a>
                        </div>
                    </div>
                @empty
                    <div class="no-data">
                        <p class="no-data-text">Không có tài khoản nào</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection