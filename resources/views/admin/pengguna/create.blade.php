<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('admin.pengguna.index', $role) }}"><i class="bi bi-arrow-left"></i></a>
        Tambah {{ $roleLabel }}
    </x-slot>

    <div class="card">
        <div class="card-header section-header"><span>Form Tambah {{ $roleLabel }}</span></div>
        <div class="card-body">
            <form action="{{ route('admin.pengguna.store', $role) }}" method="POST" enctype="multipart/form-data">
                @include('admin.pengguna._form')
            </form>
        </div>
    </div>
</x-app-layout>
