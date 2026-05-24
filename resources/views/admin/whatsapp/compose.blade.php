<x-app-layout>
    <x-slot name="header">Kirim WhatsApp</x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header section-header">
                    <span>Kirim Pesan WhatsApp</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="small text-muted">Penerima</div>
                        <div class="fs-5 fw-semibold">{{ $guru->name }}</div>
                        <div class="small text-muted">Telepon: {{ $guru->phone ?: '— belum diisi —' }}</div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <a href="{{ route('admin.wa.compose', ['guru' => $guru, 'template' => 'belum_absen']) }}" class="btn btn-outline-secondary btn-sm">Template: Belum Absen</a>
                        <a href="{{ route('admin.wa.compose', ['guru' => $guru, 'template' => 'terlambat']) }}" class="btn btn-outline-secondary btn-sm">Template: Terlambat</a>
                        <a href="{{ route('admin.wa.compose', ['guru' => $guru, 'template' => 'izin_konfirmasi']) }}" class="btn btn-outline-secondary btn-sm">Template: Konfirmasi Izin</a>
                    </div>

                    <form id="wa-form">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $guru->id }}">
                        <div class="mb-3">
                            <x-input-label for="message" value="Isi Pesan" />
                            <textarea id="message" name="message" rows="8" class="form-control font-monospace mt-1">{{ $message }}</textarea>
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            @if($guru->phone)
                                <a id="wa-button" href="{{ $wa_url }}" target="_blank" rel="noopener" class="btn btn-success">
                                    <i class="bi bi-whatsapp"></i> Buka WhatsApp
                                </a>
                            @else
                                <span class="text-danger small">Telepon belum diisi. Edit profil guru terlebih dahulu.</span>
                            @endif
                            <a href="{{ url()->previous() }}" class="small text-muted text-decoration-underline">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
    <script>
        const form = document.getElementById('wa-form');
        const btn = document.getElementById('wa-button');
        const msgInput = document.getElementById('message');
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        if (btn && msgInput) {
            msgInput.addEventListener('input', async () => {
                const res = await fetch('{{ route('admin.wa.link') }}', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                    credentials: 'same-origin',
                    body: JSON.stringify({ user_id: {{ $guru->id }}, message: msgInput.value }),
                });
                if (res.ok) {
                    const data = await res.json();
                    btn.href = data.wa_url;
                }
            });
        }
    </script>
@endpush
</x-app-layout>
