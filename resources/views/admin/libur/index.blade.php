<x-app-layout>
    <x-slot name="header">Data Hari Libur</x-slot>

    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#liburModal"
            onclick="liburCreate()">
        <i class="bi bi-plus-lg"></i> Tambah Hari Libur
    </button>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width:55px">#</th>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th class="text-center no-sort" style="width:110px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-medium">{{ $item->tanggal->format('d-m-Y') }}</td>
                                <td>{{ $item->keterangan }}</td>
                                <td class="text-center text-nowrap">
                                    <button type="button" class="btn btn-warning btn-action" title="Edit"
                                            data-bs-toggle="modal" data-bs-target="#liburModal"
                                            onclick='liburEdit(@json($item))'>
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <form action="{{ route('admin.libur.destroy', $item) }}" method="POST"
                                          class="d-inline" onsubmit="return confirm('Hapus hari libur ini?');">
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

    {{-- Modal --}}
    <div class="modal fade" id="liburModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" id="liburForm" class="modal-content">
                @csrf
                <input type="hidden" name="_method" id="liburMethod" value="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="liburTitle">Tambah Hari Libur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" id="liburTanggal" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan <span class="text-danger">*</span></label>
                        <input type="text" name="keterangan" id="liburKeterangan" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger mt-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    @push('scripts')
        <script>
            const liburStoreUrl = "{{ route('admin.libur.store') }}";
            const liburUpdateBase = "{{ url('admin/libur') }}";

            function liburCreate() {
                document.getElementById('liburTitle').textContent = 'Tambah Hari Libur';
                document.getElementById('liburForm').action = liburStoreUrl;
                document.getElementById('liburMethod').value = 'POST';
                document.getElementById('liburTanggal').value = '';
                document.getElementById('liburKeterangan').value = '';
            }

            function liburEdit(item) {
                document.getElementById('liburTitle').textContent = 'Edit Hari Libur';
                document.getElementById('liburForm').action = liburUpdateBase + '/' + item.id;
                document.getElementById('liburMethod').value = 'PUT';
                document.getElementById('liburTanggal').value = item.tanggal ? item.tanggal.substring(0, 10) : '';
                document.getElementById('liburKeterangan').value = item.keterangan;
            }

            @if($errors->any())
                new bootstrap.Modal(document.getElementById('liburModal')).show();
            @endif
        </script>
    @endpush
</x-app-layout>
