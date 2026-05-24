<x-app-layout>
    <x-slot name="header">Edit Absensi</x-slot>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header section-header">
                    <span>Edit Absensi: {{ $attendance->user->name }} ({{ $attendance->date->format('Y-m-d') }})</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.manual.update', $attendance) }}" method="POST">
                        @method('PUT')
                        @include('admin.manual._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
