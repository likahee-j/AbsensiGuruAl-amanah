<x-app-layout>
    <x-slot name="header">Data Guru</x-slot>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <a href="{{ route('admin.guru.create') }}" class="btn btn-primary mb-3">
        <i class="bi bi-plus-lg"></i> Tambah Guru
    </a>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle datatable" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width:50px">#</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th class="text-center" style="width:130px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($gurus as $g)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-medium">{{ $g->name }}</td>
                                <td>{{ $g->email }}</td>
                                <td>{{ $g->phone ?? '-' }}</td>
                                <td class="text-center text-nowrap">
                                    <a href="{{ route('admin.guru.edit', $g) }}" class="btn btn-warning btn-action text-white" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <form action="{{ route('admin.guru.destroy', $g) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus guru ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-action" title="Hapus">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
