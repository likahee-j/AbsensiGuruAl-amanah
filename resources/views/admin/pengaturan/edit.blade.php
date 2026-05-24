<x-app-layout>
    <x-slot name="header">Data Sekolah</x-slot>

    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    @endpush

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Periksa kembali isian berikut:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        {{-- Form utama: Data Sekolah + Pengaturan Absensi (satu tombol simpan) --}}
        <div class="col-lg-8">
            <form action="{{ route('admin.sekolah.update') }}" method="POST" id="formSekolah">
                @csrf @method('PUT')

                {{-- Edit Data Sekolah --}}
                <div class="card mb-4">
                    <div class="card-header section-header"><span>Edit Data Sekolah</span></div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Nama Sekolah <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" name="school_name" required
                                       class="form-control @error('school_name') is-invalid @enderror"
                                       value="{{ old('school_name', $settings->school_name) }}">
                                @error('school_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Telepon</label>
                            <div class="col-sm-9">
                                <input type="text" name="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', $settings->phone) }}">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Alamat</label>
                            <div class="col-sm-9">
                                <textarea name="address" rows="2"
                                          class="form-control @error('address') is-invalid @enderror">{{ old('address', $settings->address) }}</textarea>
                                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-3 col-form-label">Email</label>
                            <div class="col-sm-9">
                                <input type="email" name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $settings->email) }}">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pengaturan Absensi --}}
                <div class="card">
                    <div class="card-header section-header"><span>Pengaturan Absensi (Lokasi &amp; Jam)</span></div>
                    <div class="card-body">
                        <p class="small text-muted mb-2">Klik peta untuk memilih koordinat sekolah:</p>
                        <div id="map" class="w-100 rounded border mb-3" style="height:16rem;"></div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Latitude <span class="text-danger">*</span></label>
                                <input type="number" step="0.0000001" id="latitude" name="latitude" required
                                       class="form-control @error('latitude') is-invalid @enderror"
                                       value="{{ old('latitude', $settings->latitude) }}">
                                @error('latitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Longitude <span class="text-danger">*</span></label>
                                <input type="number" step="0.0000001" id="longitude" name="longitude" required
                                       class="form-control @error('longitude') is-invalid @enderror"
                                       value="{{ old('longitude', $settings->longitude) }}">
                                @error('longitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Radius (meter) <span class="text-danger">*</span></label>
                                <input type="number" min="10" max="5000" id="radius_meters" name="radius_meters" required
                                       class="form-control @error('radius_meters') is-invalid @enderror"
                                       value="{{ old('radius_meters', $settings->radius_meters) }}">
                                @error('radius_meters') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Jam Masuk Mulai <span class="text-danger">*</span></label>
                                <input type="time" name="check_in_start" required
                                       class="form-control @error('check_in_start') is-invalid @enderror"
                                       value="{{ old('check_in_start', substr((string) $settings->check_in_start, 0, 5)) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Jam Masuk Selesai <span class="text-danger">*</span></label>
                                <input type="time" name="check_in_end" required
                                       class="form-control @error('check_in_end') is-invalid @enderror"
                                       value="{{ old('check_in_end', substr((string) $settings->check_in_end, 0, 5)) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Batas Terlambat <span class="text-danger">*</span></label>
                                <input type="time" name="late_threshold" required
                                       class="form-control @error('late_threshold') is-invalid @enderror"
                                       value="{{ old('late_threshold', substr((string) $settings->late_threshold, 0, 5)) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Jam Pulang Mulai <span class="text-danger">*</span></label>
                                <input type="time" name="check_out_start" required
                                       class="form-control @error('check_out_start') is-invalid @enderror"
                                       value="{{ old('check_out_start', substr((string) $settings->check_out_start, 0, 5)) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Jam Pulang Selesai <span class="text-danger">*</span></label>
                                <input type="time" name="check_out_end" required
                                       class="form-control @error('check_out_end') is-invalid @enderror"
                                       value="{{ old('check_out_end', substr((string) $settings->check_out_end, 0, 5)) }}">
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="confirmSekolah">
                            <label class="form-check-label small" for="confirmSekolah">
                                Saya yakin akan mengubah data tersebut
                            </label>
                        </div>
                        <div id="confirmSekolahWarn" class="text-danger small mt-2 d-none">
                            <i class="bi bi-exclamation-circle"></i> Centang kotak konfirmasi terlebih dahulu untuk menyimpan.
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">
                            <i class="bi bi-save me-1"></i> Simpan Semua Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Logo (form terpisah karena upload file) --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header section-header"><span>Edit Logo Sekolah</span></div>
                <div class="card-body d-flex flex-column">
                    <div class="text-center bg-light border rounded py-2 mb-2 text-muted small">Logo</div>
                    <div class="text-center flex-grow-1 d-flex align-items-center justify-content-center py-3">
                        @if($settings->logo)
                            <img src="{{ asset('storage/'.$settings->logo) }}" alt="Logo" style="max-height:150px;max-width:100%;">
                        @else
                            <span class="text-muted"><i class="bi bi-image" style="font-size:3rem;"></i></span>
                        @endif
                    </div>
                    <form action="{{ route('admin.sekolah.logo') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <p class="fst-italic text-muted small mb-1">Ganti logo sekolah</p>
                        <div class="input-group">
                            <input type="file" name="logo" accept="image/*" required
                                   class="form-control @error('logo') is-invalid @enderror">
                            <button type="submit" class="btn btn-primary">Update</button>
                            @error('logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
        <script>
            (function () {
                const form = document.getElementById('formSekolah');
                const cb = document.getElementById('confirmSekolah');
                const warn = document.getElementById('confirmSekolahWarn');
                form.addEventListener('submit', (e) => {
                    if (!cb.checked) {
                        e.preventDefault();
                        warn.classList.remove('d-none');
                        cb.focus();
                    }
                });
                cb.addEventListener('change', () => {
                    if (cb.checked) warn.classList.add('d-none');
                });
            })();

            window.addEventListener('load', () => {
                const latInput = document.getElementById('latitude');
                const lngInput = document.getElementById('longitude');
                const radInput = document.getElementById('radius_meters');
                const initLat = parseFloat(latInput.value) || -6.2;
                const initLng = parseFloat(lngInput.value) || 106.816666;

                const map = L.map('map').setView([initLat, initLng], 16);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19, attribution: '© OpenStreetMap'
                }).addTo(map);

                const marker = L.marker([initLat, initLng], { draggable: true }).addTo(map);
                const circle = L.circle([initLat, initLng], { radius: parseInt(radInput.value) || 200, color: '#16a34a' }).addTo(map);

                function setPosition(lat, lng) {
                    latInput.value = lat.toFixed(7);
                    lngInput.value = lng.toFixed(7);
                    marker.setLatLng([lat, lng]);
                    circle.setLatLng([lat, lng]);
                }
                map.on('click', (e) => setPosition(e.latlng.lat, e.latlng.lng));
                marker.on('dragend', (e) => { const p = e.target.getLatLng(); setPosition(p.lat, p.lng); });
                radInput.addEventListener('input', () => circle.setRadius(parseInt(radInput.value) || 200));
            });
        </script>
    @endpush
</x-app-layout>
