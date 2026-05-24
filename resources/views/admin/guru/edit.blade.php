<x-app-layout>
    <x-slot name="header">Edit Guru</x-slot>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header section-header">
                    <span>Edit Data Guru</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.guru.update', $guru) }}" method="POST">
                        @method('PUT')
                        @include('admin.guru._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
