<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('admin.pengguna.show', [$role, $user]) }}"><i class="bi bi-arrow-left"></i></a>
        Edit {{ $roleLabel }}
    </x-slot>

    <div class="card">
        <div class="card-header section-header"><span>Form Edit {{ $roleLabel }}</span></div>
        <div class="card-body">
            <form action="{{ route('admin.pengguna.update', [$role, $user]) }}" method="POST" enctype="multipart/form-data">
                @include('admin.pengguna._form')
            </form>
        </div>
    </div>
</x-app-layout>
