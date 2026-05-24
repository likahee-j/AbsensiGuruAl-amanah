@php
    $isAdmin = in_array(Auth::user()->role, ['admin', 'kepsek'], true);
    $isKepsek = Auth::user()->role === 'kepsek';
    $penggunaOpen = request()->routeIs('admin.pengguna.*');
    $administrasiOpen = request()->routeIs('admin.sekolah.*', 'admin.tapel.*', 'admin.libur.*');
    $absensiOpen = request()->routeIs('admin.absensi.*', 'admin.rekap.*', 'admin.qr.*');
    $currentRole = request()->route('role');
@endphp

<aside class="app-sidebar">
    <a href="{{ route('dashboard') }}" class="sidebar-brand">
        <span class="brand-logo"><img src="{{ asset('img/logo-al-amanah.png') }}" alt="Logo Al-Amanah"></span>
        <span>{{ strtoupper(Auth::user()->roleLabel()) }}</span>
    </a>

    <nav class="sidebar-nav">
        @if($isAdmin)
            <a href="{{ route('admin.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 menu-icon"></i>
                <span>Dashboard</span>
            </a>

            <div class="sidebar-heading">Master Data</div>

            {{-- Pengguna --}}
            <button class="sidebar-link" type="button" data-bs-toggle="collapse"
                    data-bs-target="#menu-pengguna" aria-expanded="{{ $penggunaOpen ? 'true' : 'false' }}">
                <i class="bi bi-people-fill menu-icon"></i>
                <span>Pengguna</span>
                <i class="bi bi-chevron-down menu-arrow"></i>
            </button>
            <div class="collapse sidebar-submenu {{ $penggunaOpen ? 'show' : '' }}" id="menu-pengguna">
                <a href="{{ route('admin.pengguna.index', 'admin') }}"
                   class="sidebar-link {{ $penggunaOpen && $currentRole === 'admin' ? 'active' : '' }}">
                    <i class="bi bi-circle menu-icon"></i><span>Data Admin</span>
                </a>
                <a href="{{ route('admin.pengguna.index', 'kepsek') }}"
                   class="sidebar-link {{ $penggunaOpen && $currentRole === 'kepsek' ? 'active' : '' }}">
                    <i class="bi bi-circle menu-icon"></i><span>Data Kepsek</span>
                </a>
                <a href="{{ route('admin.pengguna.index', 'guru') }}"
                   class="sidebar-link {{ $penggunaOpen && $currentRole === 'guru' ? 'active' : '' }}">
                    <i class="bi bi-circle menu-icon"></i><span>Data Guru</span>
                </a>
            </div>

            {{-- Administrasi --}}
            <button class="sidebar-link" type="button" data-bs-toggle="collapse"
                    data-bs-target="#menu-administrasi" aria-expanded="{{ $administrasiOpen ? 'true' : 'false' }}">
                <i class="bi bi-folder-fill menu-icon"></i>
                <span>Administrasi</span>
                <i class="bi bi-chevron-down menu-arrow"></i>
            </button>
            <div class="collapse sidebar-submenu {{ $administrasiOpen ? 'show' : '' }}" id="menu-administrasi">
                <a href="{{ route('admin.sekolah.edit') }}"
                   class="sidebar-link {{ request()->routeIs('admin.sekolah.*') ? 'active' : '' }}">
                    <i class="bi bi-circle menu-icon"></i><span>Data Sekolah</span>
                </a>
                <a href="{{ route('admin.tapel.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.tapel.*') ? 'active' : '' }}">
                    <i class="bi bi-circle menu-icon"></i><span>Data Tapel</span>
                </a>
                <a href="{{ route('admin.libur.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.libur.*') ? 'active' : '' }}">
                    <i class="bi bi-circle menu-icon"></i><span>Data Hari Libur</span>
                </a>
            </div>

            <div class="sidebar-heading">Absensi</div>

            {{-- Absensi --}}
            <button class="sidebar-link" type="button" data-bs-toggle="collapse"
                    data-bs-target="#menu-absensi" aria-expanded="{{ $absensiOpen ? 'true' : 'false' }}">
                <i class="bi bi-calendar2-check-fill menu-icon"></i>
                <span>Absensi</span>
                <i class="bi bi-chevron-down menu-arrow"></i>
            </button>
            <div class="collapse sidebar-submenu {{ $absensiOpen ? 'show' : '' }}" id="menu-absensi">
                <a href="{{ route('admin.absensi.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.absensi.*') ? 'active' : '' }}">
                    <i class="bi bi-circle menu-icon"></i><span>Kelola Absensi</span>
                </a>
                <a href="{{ route('admin.rekap.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.rekap.*') ? 'active' : '' }}">
                    <i class="bi bi-circle menu-icon"></i><span>Rekapitulasi Absensi</span>
                </a>
                <a href="{{ route('admin.qr.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.qr.*') ? 'active' : '' }}">
                    <i class="bi bi-circle menu-icon"></i><span>QR Absensi</span>
                </a>
                <a href="{{ route('admin.kiosk.index') }}"
                   class="sidebar-link {{ request()->routeIs('admin.kiosk.*') ? 'active' : '' }}">
                    <i class="bi bi-circle menu-icon"></i><span>Mode Kios</span>
                </a>
            </div>

            @if($isKepsek)
                <div class="sidebar-heading">Absensi Saya</div>
                <a href="{{ route('absensi.index') }}"
                   class="sidebar-link {{ request()->routeIs('absensi.*') ? 'active' : '' }}">
                    <i class="bi bi-qr-code-scan menu-icon"></i>
                    <span>Absensi</span>
                </a>
                <a href="{{ route('riwayat.index') }}"
                   class="sidebar-link {{ request()->routeIs('riwayat.*') ? 'active' : '' }}">
                    <i class="bi bi-clock-history menu-icon"></i>
                    <span>Riwayat</span>
                </a>
            @endif
        @else
            <div class="sidebar-heading">Absensi</div>
            <a href="{{ route('absensi.index') }}"
               class="sidebar-link {{ request()->routeIs('absensi.*') ? 'active' : '' }}">
                <i class="bi bi-qr-code-scan menu-icon"></i>
                <span>Absensi</span>
            </a>
            <a href="{{ route('riwayat.index') }}"
               class="sidebar-link {{ request()->routeIs('riwayat.*') ? 'active' : '' }}">
                <i class="bi bi-clock-history menu-icon"></i>
                <span>Riwayat</span>
            </a>
        @endif

        <div class="sidebar-heading">Saya</div>
        <a href="{{ route('profile.edit') }}"
           class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <i class="bi bi-person-fill menu-icon"></i>
            <span>Profil</span>
        </a>
    </nav>
</aside>
