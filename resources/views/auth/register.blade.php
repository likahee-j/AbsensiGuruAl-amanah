<x-guest-layout>
    <h1 class="h5 fw-semibold text-center mb-1">Daftar Akun</h1>
    <p class="text-muted small text-center mb-4">Pendaftaran untuk akun Guru</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-3">
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <!-- Email Address -->
        <div class="mb-3">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Gender & Phone -->
        <div class="row g-3 mb-3">
            <div class="col-6">
                <x-input-label for="gender" :value="__('Jenis Kelamin')" />
                <select id="gender" name="gender" class="form-select">
                    <option value="">— Pilih —</option>
                    <option value="L" @selected(old('gender') === 'L')>Laki-laki</option>
                    <option value="P" @selected(old('gender') === 'P')>Perempuan</option>
                </select>
                <x-input-error :messages="$errors->get('gender')" class="mt-1" />
            </div>
            <div class="col-6">
                <x-input-label for="phone" :value="__('Telepon / WA')" />
                <x-text-input id="phone" type="text" name="phone" :value="old('phone')" autocomplete="tel" />
                <x-input-error :messages="$errors->get('phone')" class="mt-1" />
            </div>
        </div>

        <!-- Password -->
        <div class="mb-3">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Confirm Password -->
        <div class="mb-3">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <div class="d-grid mt-4">
            <x-primary-button class="justify-content-center">
                {{ __('Daftar') }}
            </x-primary-button>
        </div>
    </form>

    <div class="text-center small mt-4 pt-3 border-top">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="fw-semibold">Masuk di sini</a>
    </div>
</x-guest-layout>
