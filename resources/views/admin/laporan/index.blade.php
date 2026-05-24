<x-app-layout>
    <x-slot name="header">Laporan Presensi Guru</x-slot>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle datatable" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width:50px">#</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th class="text-center" style="width:130px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($gurus as $g)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-medium">{{ $g->name }}</td>
                                <td>{{ $g->email }}</td>
                                <td class="text-center text-nowrap">
                                    <a href="{{ route('admin.laporan.show', $g) }}" class="btn btn-success btn-action" title="Lihat Laporan">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
