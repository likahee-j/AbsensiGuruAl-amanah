@csrf
<div class="mb-3">
    <x-input-label for="name" value="Nama" />
    <x-text-input id="name" name="name" type="text" :value="old('name', $guru->name ?? '')" required />
    <x-input-error :messages="$errors->get('name')" />
</div>
<div class="mb-3">
    <x-input-label for="email" value="Email" />
    <x-text-input id="email" name="email" type="email" :value="old('email', $guru->email ?? '')" required />
    <x-input-error :messages="$errors->get('email')" />
</div>
<div class="mb-3">
    <x-input-label for="phone" value="Telepon (opsional)" />
    <x-text-input id="phone" name="phone" type="text" :value="old('phone', $guru->phone ?? '')" />
    <x-input-error :messages="$errors->get('phone')" />
</div>
<div class="mb-3">
    <x-input-label for="password" :value="isset($guru) ? 'Password (kosongkan jika tidak diubah)' : 'Password'" />
    <x-text-input id="password" name="password" type="password" :required="!isset($guru)" />
    <x-input-error :messages="$errors->get('password')" />
</div>
<div class="mb-3">
    <x-input-label for="password_confirmation" value="Konfirmasi Password" />
    <x-text-input id="password_confirmation" name="password_confirmation" type="password" />
</div>
<div class="d-flex align-items-center gap-3 pt-2">
    <x-primary-button>{{ isset($guru) ? 'Update' : 'Simpan' }}</x-primary-button>
    <a href="{{ route('admin.guru.index') }}" class="text-muted small">Batal</a>
</div>
