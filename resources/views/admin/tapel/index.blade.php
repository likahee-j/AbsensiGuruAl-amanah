<x-app-layout>
    <x-slot name="header">Data Tahun Pelajaran</x-slot>

    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#tapelModal"
            onclick="tapelCreate()">
        <i class="bi bi-plus-lg"></i> Tambah Tahun Pelajaran
    </button>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width:55px">#</th>
                            <th>Tahun</th>
                            <th>Semester</th>
                            <th>Mulai</th>
                            <th>Selesai</th>
                            <th class="text-center">Status</th>
                            <th class="text-center no-sort" style="width:110px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-medium">{{ $item->tahun }}</td>
                                <td>{{ $item->semester }}</td>
                                <td>{{ $item->mulai->format('d-m-Y') }}</td>
                                <td>{{ $item->selesai->format('d-m-Y') }}</td>
                                <td class="text-center">
                                    @if($item->is_aktif)
                                        <span class="badge text-bg-success">AKTIF</span>
                                    @else
                                        <span class="badge text-bg-danger">NON-AKTIF</span>
                                    @endif
                                </td>
                                <td class="text-center text-nowrap">
                                    <button type="button" class="btn btn-warning btn-action" title="Edit"
                                            data-bs-toggle="modal" data-bs-target="#tapelModal"
                                            onclick='tapelEdit(@json($item))'>
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <form action="{{ route('admin.tapel.destroy', $item) }}" method="POST"
                                          class="d-inline" onsubmit="return confirm('Hapus tahun pelajaran ini?');">
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
    <div class="modal fade" id="tapelModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" id="tapelForm" class="modal-content">
                @csrf
                <input type="hidden" name="_method" id="tapelMethod" value="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="tapelTitle">Tambah Tahun Pelajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tahun <span class="text-danger">*</span></label>
                        <input type="text" name="tahun" id="tapelTahun" class="form-control" placeholder="2024/2025" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Semester <span class="text-danger">*</span></label>
                        <select name="semester" id="tapelSemester" class="form-select" required>
                            <option value="Ganjil">Ganjil</option>
                            <option value="Genap">Genap</option>
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label">Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="mulai" id="tapelMulai" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Selesai <span class="text-danger">*</span></label>
                            <input type="date" name="selesai" id="tapelSelesai" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_aktif" value="1" id="tapelAktif">
                        <label class="form-check-label" for="tapelAktif">Tetapkan sebagai tahun pelajaran aktif</label>
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
            const tapelStoreUrl = "{{ route('admin.tapel.store') }}";
            const tapelUpdateBase = "{{ url('admin/tapel') }}";

            function tapelCreate() {
                document.getElementById('tapelTitle').textContent = 'Tambah Tahun Pelajaran';
                document.getElementById('tapelForm').action = tapelStoreUrl;
                document.getElementById('tapelMethod').value = 'POST';
                document.getElementById('tapelTahun').value = '';
                document.getElementById('tapelSemester').value = 'Ganjil';
                document.getElementById('tapelMulai').value = '';
                document.getElementById('tapelSelesai').value = '';
                document.getElementById('tapelAktif').checked = false;
            }

            function tapelEdit(item) {
                document.getElementById('tapelTitle').textContent = 'Edit Tahun Pelajaran';
                document.getElementById('tapelForm').action = tapelUpdateBase + '/' + item.id;
                document.getElementById('tapelMethod').value = 'PUT';
                document.getElementById('tapelTahun').value = item.tahun;
                document.getElementById('tapelSemester').value = item.semester;
                document.getElementById('tapelMulai').value = item.mulai ? item.mulai.substring(0, 10) : '';
                document.getElementById('tapelSelesai').value = item.selesai ? item.selesai.substring(0, 10) : '';
                document.getElementById('tapelAktif').checked = !!item.is_aktif;
            }

            @if($errors->any())
                new bootstrap.Modal(document.getElementById('tapelModal')).show();
            @endif
        </script>
    @endpush
</x-app-layout>
