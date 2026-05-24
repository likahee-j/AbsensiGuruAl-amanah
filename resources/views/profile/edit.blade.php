<x-app-layout>
    <x-slot name="header">Profil</x-slot>

    @php($status = session('status'))

    <div class="row g-4">
        {{-- Profile card --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center py-4">
                    @include('layouts.partials.avatar', ['user' => $user, 'size' => 120])
                    <h2 class="h5 mt-3 mb-1">{{ $user->name }}</h2>
                    <div class="text-muted">{{ $user->roleLabel() }}</div>
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    @if(in_array($status, ['profile-updated', 'account-updated', 'photo-updated', 'password-updated'], true))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-1"></i> Perubahan berhasil disimpan.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-profil" type="button">Edit Profil</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-foto" type="button">Edit Foto</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-qr" type="button">QR Saya</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-akun" type="button">Edit Akun</button>
                        </li>
                    </ul>

                    <div class="tab-content pt-4">
                        {{-- Edit Profil --}}
                        <div class="tab-pane fade show active" id="tab-profil">
                            <form method="POST" action="{{ route('profile.update') }}" id="formProfil">
                                @csrf @method('PATCH')
                                <div class="mb-3">
                                    <label class="form-label">Nama <span class="text-danger">*</span></label>
                                    <input type="text" name="name" required
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name', $user->name) }}">
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Jenis Kelamin</label>
                                    <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                        <option value="">— Pilih —</option>
                                        <option value="L" @selected(old('gender', $user->gender) === 'L')>Laki-laki</option>
                                        <option value="P" @selected(old('gender', $user->gender) === 'P')>Perempuan</option>
                                    </select>
                                    @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Telepon / WA</label>
                                    <input type="text" name="phone"
                                           class="form-control @error('phone') is-invalid @enderror"
                                           value="{{ old('phone', $user->phone) }}">
                                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Alamat</label>
                                    <textarea name="address" rows="2"
                                              class="form-control @error('address') is-invalid @enderror">{{ old('address', $user->address) }}</textarea>
                                    @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="confirmProfil">
                                    <label class="form-check-label small" for="confirmProfil">
                                        Saya yakin akan mengubah data tersebut
                                    </label>
                                </div>
                                <div id="confirmProfilWarn" class="text-danger small mb-3 d-none">
                                    <i class="bi bi-exclamation-circle"></i> Centang kotak konfirmasi terlebih dahulu untuk menyimpan.
                                </div>
                                <button type="submit" class="btn btn-primary" id="submitProfil">Simpan</button>
                            </form>
                        </div>

                        {{-- Edit Foto --}}
                        <div class="tab-pane fade" id="tab-foto">
                            <form method="POST" action="{{ route('profile.photo') }}" enctype="multipart/form-data">
                                @csrf @method('PATCH')
                                <div class="mb-3 text-center">
                                    @include('layouts.partials.avatar', ['user' => $user, 'size' => 130])
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Pilih Foto Baru</label>
                                    <input type="file" name="photo" accept="image/*" required
                                           class="form-control @error('photo') is-invalid @enderror">
                                    @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <small class="text-muted">Format gambar, maksimal 2 MB.</small>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Foto</button>
                            </form>
                        </div>

                        {{-- QR Saya --}}
                        <div class="tab-pane fade" id="tab-qr">
                            <div class="text-center">
                                <p class="text-muted mb-3">
                                    QR Code identitas Anda untuk absensi via Mode Kios.
                                    Tunjukkan ke kamera laptop sekolah saat absen.
                                </p>
                                <img src="{{ route('profile.qr') }}" alt="QR Code Saya"
                                     style="width:280px;max-width:100%;">
                                <div class="text-muted small mt-3">{{ $user->username ?: 'USER-'.$user->id }}</div>
                                <div class="mt-3">
                                    <a href="{{ route('profile.qr') }}"
                                       download="qr-{{ \Illuminate\Support\Str::slug($user->name) }}.svg"
                                       class="btn btn-primary">
                                        <i class="bi bi-download me-1"></i>Download QR
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Edit Akun --}}
                        <div class="tab-pane fade" id="tab-akun">
                            <form method="POST" action="{{ route('profile.account') }}" class="mb-4">
                                @csrf @method('PATCH')
                                <h6 class="text-muted text-uppercase small fw-bold mb-3">Informasi Akun</h6>
                                <div class="mb-3">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" required
                                           class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email', $user->email) }}">
                                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username"
                                           class="form-control @error('username') is-invalid @enderror"
                                           value="{{ old('username', $user->username) }}">
                                    @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <button type="submit" class="btn btn-primary">Simpan Akun</button>
                            </form>
                            <hr>
                            <h6 class="text-muted text-uppercase small fw-bold mb-3">Ubah Password</h6>
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        (function () {
            const form = document.getElementById('formProfil');
            const cb = document.getElementById('confirmProfil');
            const warn = document.getElementById('confirmProfilWarn');
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
    </script>
    @endpush
</x-app-layout>
