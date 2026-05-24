<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('admin.pengguna.index', $role) }}"><i class="bi bi-arrow-left"></i></a>
        Data {{ $roleLabel }}
    </x-slot>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header section-header">
                    <span>Detail Data {{ $roleLabel }}</span>
                    <a href="{{ route('admin.pengguna.edit', [$role, $user]) }}" class="btn btn-warning text-white">
                        <i class="bi bi-pencil-fill me-1"></i>Edit Data
                    </a>
                </div>
                <div class="card-body">
                    <div class="text-center py-3">
                        @include('layouts.partials.avatar', ['user' => $user, 'size' => 110])
                        <h2 class="h5 mt-3 mb-0">{{ $user->name }}</h2>
                        <div class="text-muted small">{{ $user->roleLabel() }}</div>
                    </div>

                    <table class="table table-sm align-middle mb-0">
                        <tbody>
                            <tr><th class="text-muted" style="width:140px">Email</th><td>: {{ $user->email ?: '-' }}</td></tr>
                            <tr><th class="text-muted">NIP</th><td>: {{ $user->nip ?: '-' }}</td></tr>
                            <tr><th class="text-muted">NUPTK</th><td>: {{ $user->nuptk ?: '-' }}</td></tr>
                            <tr><th class="text-muted">Jenis Kelamin</th><td>: {{ $user->genderLabel() }}</td></tr>
                            <tr><th class="text-muted">Telepon / WA</th><td>: {{ $user->phone ?: '-' }}</td></tr>
                            <tr><th class="text-muted">Alamat</th><td>: {{ $user->address ?: '-' }}</td></tr>
                            <tr><th class="text-muted">Username</th><td>: {{ $user->username ?: '-' }}</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header section-header">
                    <span>QR Code</span>
                    <a href="{{ route('admin.pengguna.qr', [$role, $user]) }}"
                       download="qr-{{ \Illuminate\Support\Str::slug($user->name) }}.svg" class="btn btn-primary">
                        <i class="bi bi-download me-1"></i>Download
                    </a>
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                    <img src="{{ route('admin.pengguna.qr', [$role, $user]) }}" alt="QR Code"
                         style="width:280px;max-width:100%;">
                    <div class="text-muted small mt-3">{{ $user->username ?: 'USER-'.$user->id }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
