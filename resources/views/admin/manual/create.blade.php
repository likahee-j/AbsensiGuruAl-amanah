<x-app-layout>
    <x-slot name="header">Catat Absensi Manual</x-slot>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header section-header">
                    <span>Catat Absensi Manual</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.manual.store') }}" method="POST">
                        @include('admin.manual._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
