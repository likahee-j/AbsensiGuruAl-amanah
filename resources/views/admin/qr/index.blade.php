<x-app-layout>
    <x-slot name="header">QR Absensi</x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header section-header">
                    <span>QR Absensi (Auto-refresh setiap 30 detik)</span>
                </div>
                <div class="card-body text-center p-4 p-sm-5">
                    <div class="mb-4">
                        <div class="small text-uppercase text-muted fw-semibold ls-wide">Scan Untuk Absen</div>
                        <div id="school-name" class="mt-2 fs-3 fw-bold">
                            {{ \App\Models\SchoolSetting::current()->school_name }}
                        </div>
                    </div>

                    <div class="d-flex flex-column align-items-center">
                        <div id="qr-frame" class="bg-white p-3 rounded border shadow-sm">
                            <div id="qr-canvas" style="width:420px;height:420px;max-width:100%;" class="d-flex align-items-center justify-content-center">
                                <span class="text-muted">Memuat QR...</span>
                            </div>
                        </div>

                        <div class="mt-4 d-flex align-items-center gap-3">
                            <div class="fs-5 text-muted">QR berganti dalam</div>
                            <div id="countdown" class="display-4 fw-bold text-primary" style="min-width:4rem;">30</div>
                            <div class="fs-5 text-muted">detik</div>
                        </div>

                        <div id="qr-token-hint" class="mt-2 small text-muted text-break"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
    <script>
        (function () {
            const canvas = document.getElementById('qr-canvas');
            const countdownEl = document.getElementById('countdown');
            const hintEl = document.getElementById('qr-token-hint');
            let countdown = 30;
            let tickHandle = null;

            async function fetchQr() {
                try {
                    const res = await fetch('{{ route('admin.qr.generate') }}', {
                        headers: { 'Accept': 'application/json' },
                        credentials: 'same-origin'
                    });
                    if (!res.ok) throw new Error('Gagal memuat QR');
                    const data = await res.json();
                    canvas.innerHTML = atob(data.qr_svg_base64);
                    const svg = canvas.querySelector('svg');
                    if (svg) {
                        svg.setAttribute('width', '400');
                        svg.setAttribute('height', '400');
                    }
                    hintEl.textContent = 'Token: ' + data.token;
                    countdown = data.ttl_seconds || 30;
                    countdownEl.textContent = countdown;
                } catch (e) {
                    canvas.innerHTML = '<span class="text-danger">Error: ' + e.message + '</span>';
                }
            }

            function tick() {
                countdown--;
                if (countdown <= 0) {
                    fetchQr();
                    return;
                }
                countdownEl.textContent = countdown;
            }

            fetchQr().then(() => {
                tickHandle = setInterval(tick, 1000);
            });
        })();
    </script>
@endpush
</x-app-layout>
