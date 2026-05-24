<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'AbsensiGuru') }} — Sistem Absensi Guru Al-Amanah</title>
    @include('layouts.assets')
    <style>
        .hero-section {
            background: linear-gradient(160deg, var(--brand) 0%, var(--brand-dark) 100%);
            padding: 5rem 0 4rem;
        }
        .hero-section .lead {
            opacity: .9;
        }
        .feature-icon {
            width: 3.5rem;
            height: 3.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: .75rem;
            background-color: var(--brand-soft);
            color: var(--brand);
            font-size: 1.5rem;
            margin-bottom: 1rem;
            flex-shrink: 0;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #fff;
        }
        .stat-label {
            font-size: .875rem;
            color: rgba(255,255,255,.8);
        }
        .navbar-brand-icon {
            width: 2rem;
            height: 2rem;
            background-color: rgba(255,255,255,.2);
            border-radius: .5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }
        .section-label {
            font-size: .75rem;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--brand);
        }
        footer {
            background-color: #1e293b;
            color: rgba(255,255,255,.7);
        }
        footer a {
            color: rgba(255,255,255,.7);
            text-decoration: none;
        }
        footer a:hover {
            color: #fff;
        }
    </style>
</head>
<body>

    {{-- ===== NAVBAR ===== --}}
    <nav class="navbar navbar-expand-md bg-primary navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="{{ url('/') }}">
                <span class="navbar-brand-icon">
                    <i class="bi bi-person-badge text-white"></i>
                </span>
                AbsensiGuru
            </a>

            <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-md-0">
                    <li class="nav-item">
                        <a class="nav-link" href="#fitur">Fitur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#cara-kerja">Cara Kerja</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#kontak">Kontak</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-light btn-sm fw-semibold">
                                <i class="bi bi-speedometer2 me-1"></i>Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">
                                Masuk
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-light btn-sm fw-semibold">
                                    Daftar
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    {{-- ===== HERO ===== --}}
    <section class="hero-section text-white">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-7">
                    <span class="badge bg-white text-primary fw-semibold mb-3 px-3 py-2">
                        <i class="bi bi-shield-check me-1"></i>Sistem Absensi Digital
                    </span>
                    <h1 class="display-5 fw-bold mb-3">
                        Absensi Guru Modern<br>untuk <span style="opacity:.85;">Al-Amanah</span>
                    </h1>
                    <p class="lead mb-4" style="max-width:520px;">
                        Kelola kehadiran guru secara efisien dengan teknologi QR Code, verifikasi GPS, dan laporan otomatis langsung ke WhatsApp — semua dalam satu platform terintegrasi.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn btn-light btn-lg fw-semibold px-4">
                                    <i class="bi bi-speedometer2 me-2"></i>Buka Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-light btn-lg fw-semibold px-4">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk Sekarang
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg px-4">
                                        Pelajari Lebih Lanjut
                                    </a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block text-center">
                    <div class="p-4 rounded-4" style="background:rgba(255,255,255,.1);">
                        <i class="bi bi-qr-code-scan" style="font-size:8rem;opacity:.9;"></i>
                        <p class="mt-3 mb-0 fw-semibold" style="font-size:.95rem;">Scan QR — Absensi Selesai</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== STATS STRIP ===== --}}
    <section style="background:var(--brand-dark);" class="py-4">
        <div class="container">
            <div class="row text-center g-3">
                <div class="col-6 col-md-3">
                    <div class="stat-number">100+</div>
                    <div class="stat-label">Guru Terdaftar</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-number">99%</div>
                    <div class="stat-label">Akurasi Data</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Tersedia Online</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="stat-number">0</div>
                    <div class="stat-label">Kertas Dibutuhkan</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== FEATURES ===== --}}
    <section id="fitur" class="py-5">
        <div class="container py-3">
            <div class="text-center mb-5">
                <span class="section-label">Fitur Unggulan</span>
                <h2 class="fw-bold mt-2 mb-3">Semua yang Anda Butuhkan</h2>
                <p class="text-muted mx-auto" style="max-width:520px;">
                    Platform AbsensiGuru dirancang khusus untuk kebutuhan madrasah dan sekolah Islam, menghadirkan teknologi modern dalam pengelolaan kehadiran guru.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 p-4">
                        <div class="feature-icon">
                            <i class="bi bi-qr-code-scan"></i>
                        </div>
                        <h5 class="fw-semibold mb-2">Absensi QR Code</h5>
                        <p class="text-muted mb-0">
                            Guru cukup scan QR Code yang tersedia di pintu masuk. Proses absensi selesai dalam hitungan detik, tanpa antrian dan tanpa kontak fisik.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 p-4">
                        <div class="feature-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <h5 class="fw-semibold mb-2">Verifikasi GPS</h5>
                        <p class="text-muted mb-0">
                            Sistem memverifikasi lokasi guru saat melakukan absensi secara otomatis. Hanya bisa absen ketika berada dalam radius area sekolah yang telah ditentukan.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 p-4">
                        <div class="feature-icon">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <h5 class="fw-semibold mb-2">Rekap Bulanan Otomatis</h5>
                        <p class="text-muted mb-0">
                            Laporan kehadiran direkap secara otomatis setiap akhir bulan. Data tersaji dalam format yang rapi dan mudah diekspor untuk keperluan administrasi.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 p-4">
                        <div class="feature-icon">
                            <i class="bi bi-whatsapp"></i>
                        </div>
                        <h5 class="fw-semibold mb-2">Laporan via WhatsApp</h5>
                        <p class="text-muted mb-0">
                            Notifikasi dan laporan absensi dikirim otomatis ke WhatsApp guru dan kepala sekolah. Informasi real-time tanpa perlu buka aplikasi.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 p-4">
                        <div class="feature-icon">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <h5 class="fw-semibold mb-2">Manajemen Data Guru</h5>
                        <p class="text-muted mb-0">
                            Kelola profil lengkap seluruh guru — mata pelajaran, jadwal, golongan, dan riwayat kehadiran — dalam satu dashboard yang terintegrasi dan mudah digunakan.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 p-4">
                        <div class="feature-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h5 class="fw-semibold mb-2">Keamanan Data Terjamin</h5>
                        <p class="text-muted mb-0">
                            Data absensi diproteksi dengan sistem autentikasi berlapis. Hak akses berbeda untuk guru, staf TU, dan kepala sekolah demi keamanan informasi.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== HOW IT WORKS ===== --}}
    <section id="cara-kerja" style="background-color:var(--brand-soft);" class="py-5">
        <div class="container py-3">
            <div class="text-center mb-5">
                <span class="section-label">Cara Kerja</span>
                <h2 class="fw-bold mt-2 mb-3">Mudah dalam 3 Langkah</h2>
                <p class="text-muted mx-auto" style="max-width:480px;">
                    Tidak perlu pelatihan panjang. Sistem AbsensiGuru dirancang agar langsung bisa digunakan oleh semua guru.
                </p>
            </div>

            <div class="row g-4 justify-content-center">
                <div class="col-md-4 text-center">
                    <div class="rounded-circle text-white d-inline-flex align-items-center justify-content-center mb-3"
                        style="width:3.5rem;height:3.5rem;background:var(--brand);font-size:1.25rem;font-weight:700;">
                        1
                    </div>
                    <h5 class="fw-semibold mb-2">Buka Aplikasi</h5>
                    <p class="text-muted mb-0">
                        Guru membuka halaman absensi dari browser ponsel atau komputer. Login dengan akun yang telah didaftarkan oleh admin sekolah.
                    </p>
                </div>

                <div class="col-md-4 text-center">
                    <div class="rounded-circle text-white d-inline-flex align-items-center justify-content-center mb-3"
                        style="width:3.5rem;height:3.5rem;background:var(--brand);font-size:1.25rem;font-weight:700;">
                        2
                    </div>
                    <h5 class="fw-semibold mb-2">Scan QR Code</h5>
                    <p class="text-muted mb-0">
                        Arahkan kamera ke QR Code yang terpasang di area sekolah. Sistem akan memverifikasi lokasi GPS secara otomatis saat itu juga.
                    </p>
                </div>

                <div class="col-md-4 text-center">
                    <div class="rounded-circle text-white d-inline-flex align-items-center justify-content-center mb-3"
                        style="width:3.5rem;height:3.5rem;background:var(--brand);font-size:1.25rem;font-weight:700;">
                        3
                    </div>
                    <h5 class="fw-semibold mb-2">Absensi Tercatat</h5>
                    <p class="text-muted mb-0">
                        Data kehadiran langsung tersimpan ke sistem dan notifikasi dikirim via WhatsApp. Laporan bulanan tersedia kapan saja untuk diunduh.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== CTA ===== --}}
    <section class="py-5">
        <div class="container py-3">
            <div class="card text-white text-center p-5" style="background:linear-gradient(160deg, var(--brand) 0%, var(--brand-dark) 100%);border-radius:1.25rem;">
                <h2 class="fw-bold mb-3">Mulai Gunakan AbsensiGuru Sekarang</h2>
                <p class="lead mb-4" style="opacity:.9;max-width:500px;margin-inline:auto;">
                    Tingkatkan efisiensi administrasi sekolah Al-Amanah dengan sistem absensi digital yang andal dan mudah digunakan.
                </p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-light btn-lg fw-semibold px-4">
                                <i class="bi bi-speedometer2 me-2"></i>Buka Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-light btn-lg fw-semibold px-4">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Masuk ke Sistem
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg px-4">
                                    Daftar Akun
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ===== FOOTER ===== --}}
    <footer id="kontak" class="py-4">
        <div class="container">
            <div class="row align-items-center g-3">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-person-badge text-white"></i>
                        <span class="fw-semibold text-white">AbsensiGuru</span>
                    </div>
                    <small>Sistem Absensi Digital — Madrasah Al-Amanah</small>
                </div>
                <div class="col-md-6 text-md-end">
                    <small>&copy; {{ date('Y') }} Al-Amanah. Hak cipta dilindungi.</small>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
