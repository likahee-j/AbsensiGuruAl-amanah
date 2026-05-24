<x-app-layout>
    <x-slot name="header">Tambah Guru</x-slot>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header section-header">
                    <span>Tambah Guru</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.guru.store') }}" method="POST">
                        @include('admin.guru._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
