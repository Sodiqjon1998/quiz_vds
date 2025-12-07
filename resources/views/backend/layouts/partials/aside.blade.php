<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme border-end">

    {{-- BRAND LOGO --}}
    <div class="app-brand demo py-3">
        <a href="{{ route('dashboard') }}" class="app-brand-link gap-2">
            <span class="app-brand-logo demo">
                <img src="{{asset('images/staticImages/logo.png')}}" alt="Logo" style="width:40px; height:auto;">
            </span>
            <span class="app-brand-text demo menu-text fw-bold text-dark ms-1" style="font-size: 1.3rem;">Yuksalish</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ri-menu-fold-line d-block d-xl-none align-middle"></i>
            <i class="ri-radio-button-line d-none d-xl-block align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    {{-- MENU LIST --}}
    <ul class="menu-inner py-1">

        {{-- 1. BOSH SAHIFA --}}
        <li class="menu-item {{ Route::is('dashboard') ? 'active' : '' }}">
            <a href="{{route('dashboard')}}" class="menu-link">
                <i class="menu-icon tf-icons ri-dashboard-3-line"></i>
                <div data-i18n="Bosh sahifa">Bosh Sahifa</div>
            </a>
        </li>

        {{-- SECTION: TAHLIL --}}
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text text-muted">Tahlil va Hisobotlar</span>
        </li>

        {{-- Sinf Ko'rsatkichlari --}}
        <li class="menu-item {{ request()->routeIs('backend.reports.class-performance') ? 'active' : '' }}">
            <a href="{{ route('backend.reports.class-performance') }}" class="menu-link">
                <i class="menu-icon tf-icons ri-bar-chart-grouped-line"></i>
                <div data-i18n="Sinf Ko'rsatkichlari">Sinf Ko'rsatkichlari</div>
            </a>
        </li>

        {{-- Test Natijalari --}}
        <li class="menu-item {{ request()->routeIs('backend.reports.exam-results') ? 'active' : '' }}">
            <a href="{{ route('backend.reports.exam-results') }}" class="menu-link">
                <i class="menu-icon tf-icons ri-file-paper-2-line"></i>
                <div data-i18n="Test Natijalari">Test Natijalari</div>
            </a>
        </li>

        {{-- SECTION: BOSHQARUV --}}
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text text-muted">Maktab Boshqaruvi</span>
        </li>

        {{-- Foydalanuvchilar (Dropdown) --}}
        <li class="menu-item {{ Route::is('backend.users*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ri-user-settings-line"></i>
                <div data-i18n="Foydalanuvchilar">Foydalanuvchilar</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ Route::is('backend.users.index') ? 'active' : '' }}">
                    <a href="{{ route('backend.users.index') }}" class="menu-link">
                        <div data-i18n="Xodimlar">O'qituvchi va Kordinator</div>
                    </a>
                </li>
            </ul>
        </li>

        {{-- Sinflar --}}
        <li class="menu-item {{ Route::is('backend.classes.index') ? 'active' : '' }}">
            <a href="{{route('backend.classes.index')}}" class="menu-link">
                <i class="menu-icon tf-icons ri-building-4-line"></i>
                <div data-i18n="Sinflar">Sinflar</div>
            </a>
        </li>

        {{-- Fanlar --}}
        <li class="menu-item {{ Route::is('backend.subjects.index') ? 'active' : '' }}">
            <a href="{{route('backend.subjects.index')}}" class="menu-link">
                <i class="menu-icon tf-icons ri-book-open-line"></i>
                <div data-i18n="Fanlar">Fanlar</div>
            </a>
        </li>

        {{-- SECTION: O'QUVCHILAR --}}
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text text-muted">O'quvchilar</span>
        </li>

        {{-- O'quvchilar Ro'yxati --}}
        <li class="menu-item {{ Route::is('backend.student.index') ? 'active' : '' }}">
            <a href="{{route('backend.student.index')}}" class="menu-link">
                <i class="menu-icon tf-icons ri-graduation-cap-line"></i>
                <div data-i18n="O'quvchilar">O'quvchilar</div>
            </a>
        </li>

        {{-- Yangi O'quvchilar --}}
        <li class="menu-item {{ Route::is('backend.users.newStudent') ? 'active' : '' }}">
            <a href="{{route('backend.users.newStudent')}}" class="menu-link">
                <i class="menu-icon tf-icons ri-user-add-line"></i>
                <div data-i18n="Yangi arizalar">Yangi arizalar</div>
                {{-- Agar yangi o'quvchilar soni bo'lsa, bu yerga badge qo'shish mumkin --}}
                {{-- <span class="badge bg-danger rounded-pill ms-auto">5</span> --}}
            </a>
        </li>

    </ul>
</aside>

{{-- CSS STYLES (Yuksalish Orange Theme) --}}
<style>
    /* Asosiy menyu rangi va shrift */
    .bg-menu-theme {
        background-color: #ffffff !important;
    }

    .bg-menu-theme .menu-link {
        color: #566a7f;
        margin: 0.2rem 1rem;
        border-radius: 0.5rem;
    }

    /* Hover effekti */
    .bg-menu-theme .menu-link:hover {
        background-color: #fff0e6 !important;
        /* Och zarg'aldoq */
        color: #F58025 !important;
    }

    /* Active (Tanlangan) holat */
    .bg-menu-theme .menu-item.active>.menu-link {
        background-color: #fff0e6 !important;
        /* Och zarg'aldoq fon */
        color: #F58025 !important;
        /* To'q zarg'aldoq yozuv */
        font-weight: 600;
        box-shadow: none !important;
    }

    /* Active holatdagi ikonka */
    .bg-menu-theme .menu-item.active>.menu-link i {
        color: #F58025 !important;
    }

    /* Menu Header (Bo'lim nomlari) */
    .menu-header {
        margin-top: 1rem;
        margin-bottom: 0.5rem;
        padding-left: 1.8rem;
    }

    .menu-header-text {
        font-size: 0.75rem;
        font-weight: 700;
        color: #b4bdc6 !important;
        letter-spacing: 0.5px;
    }

    /* Logo matni */
    .app-brand-text {
        color: #434343 !important;
        letter-spacing: -0.5px;
    }

    /* Ikonka o'lchamlari */
    .menu-icon {
        font-size: 1.4rem;
    }
</style>