<x-app-layout>
    <x-slot name="header">Data {{ $roleLabel }}</x-slot>

    <a href="{{ route('admin.pengguna.create', $role) }}" class="btn btn-primary mb-3">
        <i class="bi bi-plus-lg"></i> Tambah {{ $roleLabel }}
    </a>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width:60px">#</th>
                            <th>Nama</th>
                            <th style="width:80px">L/P</th>
                            <th class="text-center no-sort" style="width:160px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $u)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-medium">{{ $u->name }}</td>
                                <td>{{ $u->gender ?: '-' }}</td>
                                <td class="text-center text-nowrap">
                                    <a href="{{ route('admin.pengguna.show', [$role, $u]) }}"
                                       class="btn btn-success btn-action" title="Detail">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <a href="{{ route('admin.pengguna.edit', [$role, $u]) }}"
                                       class="btn btn-warning btn-action" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <form action="{{ route('admin.pengguna.destroy', [$role, $u]) }}" method="POST"
                                          class="d-inline" onsubmit="return confirm('Hapus {{ $roleLabel }} ini?');">
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
