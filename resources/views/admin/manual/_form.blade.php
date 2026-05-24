@csrf
<div class="row g-3 mb-3">
    <div class="col-md-6">
        <x-input-label value="Guru" />
        @if(isset($attendance))
            <input type="text" disabled value="{{ $attendance->user->name }}" class="form-control bg-light">
        @else
            <select name="user_id" class="form-select" required>
                <option value="">— Pilih Guru —</option>
                @foreach($gurus as $g)
                    <option value="{{ $g->id }}" @selected(($defaults['user_id'] ?? old('user_id')) == $g->id)>{{ $g->name }}</option>
                @endforeach
            </select>
        @endif
        <x-input-error :messages="$errors->get('user_id')" />
    </div>
    <div class="col-md-6">
        <x-input-label for="date" value="Tanggal" />
        @if(isset($attendance))
            <input type="text" disabled value="{{ $attendance->date->format('Y-m-d') }}" class="form-control bg-light">
        @else
            <x-text-input id="date" name="date" type="date" :value="old('date', $defaults['date'] ?? today()->toDateString())" required />
        @endif
        <x-input-error :messages="$errors->get('date')" />
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <x-input-label for="status" value="Status" />
        <select id="status" name="status" class="form-select" required>
            @foreach($statuses as $key => $label)
                <option value="{{ $key }}" @selected(old('status', $attendance->status ?? '') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('status')" />
    </div>
    <div class="col-md-4">
        <x-input-label for="check_in_time" value="Jam Masuk (opsional)" />
        <x-text-input id="check_in_time" name="check_in_time" type="time" :value="old('check_in_time', isset($attendance) && $attendance->check_in_time ? substr($attendance->check_in_time, 0, 5) : '')" />
        <x-input-error :messages="$errors->get('check_in_time')" />
    </div>
    <div class="col-md-4">
        <x-input-label for="check_out_time" value="Jam Pulang (opsional)" />
        <x-text-input id="check_out_time" name="check_out_time" type="time" :value="old('check_out_time', isset($attendance) && $attendance->check_out_time ? substr($attendance->check_out_time, 0, 5) : '')" />
        <x-input-error :messages="$errors->get('check_out_time')" />
    </div>
</div>

<div class="mb-3">
    <x-input-label for="notes" value="Catatan (opsional)" />
    <textarea id="notes" name="notes" rows="3" class="form-control">{{ old('notes', $attendance->notes ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('notes')" />
</div>

<div class="d-flex align-items-center gap-3 pt-2">
    <x-primary-button>{{ isset($attendance) ? 'Update' : 'Simpan' }}</x-primary-button>
    <a href="{{ route('admin.manual.index') }}" class="text-muted small">Batal</a>
</div>
