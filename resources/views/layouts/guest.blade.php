<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        @include('layouts.assets')
        @stack('styles')
    </head>
    <body>
        <div class="min-vh-100 d-flex flex-column justify-content-center align-items-center py-5 px-3"
             style="background: linear-gradient(160deg, var(--brand) 0%, var(--brand-dark) 100%);">

            <a href="/" class="text-decoration-none text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle shadow p-3"
                     style="width:6.5rem;height:6.5rem;">
                    <img src="{{ asset('img/logo-al-amanah.png') }}" alt="Logo Al-Amanah"
                         style="max-width:100%;max-height:100%;object-fit:contain;">
                </div>
                <div class="mt-2 text-white fw-semibold fs-5">Sekolah Islam Al-Amanah</div>
            </a>

            <div class="card shadow-lg w-100" style="max-width:28rem;">
                <div class="card-body p-4 p-sm-5">
                    {{ $slot }}
                </div>
            </div>

            <div class="text-white-50 small mt-4">&copy; al-amanahxlika 2026</div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        @stack('scripts')
    </body>
</html>
