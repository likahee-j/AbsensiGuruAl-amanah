@csrf
@isset($user) @method('PUT') @endisset

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $user->name ?? '') }}" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Jenis Kelamin</label>
        <select name="gender" class="form-select @error('gender') is-invalid @enderror">
            <option value="">— Pilih —</option>
            <option value="L" @selected(old('gender', $user->gender ?? '') === 'L')>Laki-laki</option>
            <option value="P" @selected(old('gender', $user->gender ?? '') === 'P')>Perempuan</option>
        </select>
        @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $user->email ?? '') }}" required>
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
               value="{{ old('username', $user->username ?? '') }}">
        @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">NIP</label>
        <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror"
               value="{{ old('nip', $user->nip ?? '') }}">
        @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">NUPTK</label>
        <input type="text" name="nuptk" class="form-control @error('nuptk') is-invalid @enderror"
               value="{{ old('nuptk', $user->nuptk ?? '') }}">
        @error('nuptk') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Telepon / WA</label>
        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
               value="{{ old('phone', $user->phone ?? '') }}">
        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Foto</label>
        <input type="file" name="photo" accept="image/*" class="form-control @error('photo') is-invalid @enderror">
        @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Alamat</label>
        <textarea name="address" rows="2" class="form-control @error('address') is-invalid @enderror">{{ old('address', $user->address ?? '') }}</textarea>
        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Password @if(!isset($user))<span class="text-danger">*</span>@endif</label>
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
               {{ isset($user) ? '' : 'required' }}>
        @isset($user) <small class="text-muted">Kosongkan jika tidak diubah.</small> @endisset
        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" class="form-control">
    </div>
</div>

<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
    <a href="{{ route('admin.pengguna.index', $role) }}" class="btn btn-light border">Batal</a>
</div>
