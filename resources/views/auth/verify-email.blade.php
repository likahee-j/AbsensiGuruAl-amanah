<x-guest-layout>
    <h1 class="h5 fw-semibold text-center mb-4">Verifikasi Email</h1>

    <p class="small text-muted mb-3">
        {{ __('Terima kasih telah mendaftar! Sebelum memulai, harap verifikasi alamat email Anda dengan mengklik tautan yang telah kami kirimkan. Jika Anda tidak menerimanya, kami akan mengirimkan yang baru.') }}
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success py-2 small mb-3">
            {{ __('Tautan verifikasi baru telah dikirimkan ke alamat email yang Anda berikan saat pendaftaran.') }}
        </div>
    @endif

    <div class="d-flex align-items-center justify-content-between mt-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <x-primary-button>
                {{ __('Kirim Ulang Email Verifikasi') }}
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="btn btn-link small text-muted p-0">
                {{ __('Keluar') }}
            </button>
        </form>
    </div>
</x-guest-layout>
