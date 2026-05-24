<x-app-layout>
    <x-slot name="header">Absensi Hari Ini</x-slot>

    @php
        $hasCheckIn  = $todayAttendance && $todayAttendance->check_in_time;
        $hasCheckOut = $todayAttendance && $todayAttendance->check_out_time;
        $isLeaveType = $todayAttendance && in_array($todayAttendance->status, ['izin', 'sakit', 'alpa'], true);
        $needScan    = ! $isLeaveType && (! $hasCheckIn || ! $hasCheckOut);
        $mode        = ! $hasCheckIn ? 'checkin' : 'checkout';
        $modeLabel   = $mode === 'checkin' ? 'Masuk' : 'Pulang';
    @endphp

    @push('styles')
    <style>
        .scan-modal { border: 0; border-radius: 18px; overflow: hidden; }
        .scan-header {
            background: linear-gradient(135deg, #15803d, #22c55e); color: #fff;
            border: 0; align-items: center; gap: .75rem; padding: 1rem 1.1rem;
        }
        .scan-head-icon {
            width: 44px; height: 44px; border-radius: 12px; background: rgba(255,255,255,.22);
            display: flex; align-items: center; justify-content: center; font-size: 1.35rem; flex: 0 0 auto;
        }
        .scan-subtitle { font-size: .78rem; opacity: .9; }
        .scan-close {
            border: 0; background: rgba(0,0,0,.18); color: #fff;
            width: 36px; height: 36px; border-radius: 10px; flex: 0 0 auto;
        }
        .scan-viewport {
            position: relative; width: 100%; aspect-ratio: 1 / 1;
            background: #0f1729; border-radius: 16px; overflow: hidden;
        }
        #reader { position: absolute; inset: 0; }
        #reader video { width: 100% !important; height: 100% !important; object-fit: cover; }
        .scan-frame { position: absolute; inset: 15%; }
        .scan-frame .corner { position: absolute; width: 48px; height: 48px; }
        .scan-frame .tl { top: 0; left: 0; border-top: 5px solid #22c55e; border-left: 5px solid #22c55e; border-top-left-radius: 12px; }
        .scan-frame .tr { top: 0; right: 0; border-top: 5px solid #22c55e; border-right: 5px solid #22c55e; border-top-right-radius: 12px; }
        .scan-frame .bl { bottom: 0; left: 0; border-bottom: 5px solid #22c55e; border-left: 5px solid #22c55e; border-bottom-left-radius: 12px; }
        .scan-frame .br { bottom: 0; right: 0; border-bottom: 5px solid #22c55e; border-right: 5px solid #22c55e; border-bottom-right-radius: 12px; }
        .scan-frame::before {
            content: ''; position: absolute; inset: 0; border: 1px solid rgba(255,255,255,.18); border-radius: 8px;
        }
        .scan-line {
            position: absolute; left: 4%; right: 4%; height: 3px;
            background: linear-gradient(90deg, transparent, #22c55e, transparent);
            box-shadow: 0 0 14px 2px #22c55e; animation: scanmove 2.4s ease-in-out infinite;
        }
        @keyframes scanmove { 0%, 100% { top: 4%; } 50% { top: 93%; } }
        .scan-status {
            position: absolute; left: 50%; bottom: 14px; transform: translateX(-50%);
            background: rgba(15,23,41,.92); color: #dbe4ee; padding: .42rem .95rem;
            border-radius: 999px; font-size: .78rem; white-space: nowrap; display: flex;
            align-items: center; gap: .4rem;
        }
        .btn-simulasi {
            width: 100%; border: 0; border-radius: 12px; padding: .8rem 1rem;
            font-weight: 600; color: #fff; background: linear-gradient(135deg, #16a34a, #22c55e);
            box-shadow: 0 6px 16px rgba(22,163,74,.35);
        }
        .btn-simulasi:disabled { opacity: .6; }
    </style>
    @endpush

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-header section-header"><span>Absensi Hari Ini</span></div>
                <div class="card-body p-4">
                    @if($isLeaveType)
                        <div class="text-center">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success-subtle text-success mb-2"
                                 style="width:3.5rem;height:3.5rem;">
                                <i class="bi bi-info-circle-fill fs-4"></i>
                            </div>
                            <h2 class="h5 fw-semibold mb-1">Status hari ini: {{ \App\Models\Attendance::STATUS_LABELS[$todayAttendance->status] }}</h2>
                            @if($todayAttendance->notes)
                                <p class="text-muted small mb-0">Catatan: {{ $todayAttendance->notes }}</p>
                            @endif
                        </div>
                    @else
                        <div class="text-center">
                            <div class="text-muted">{{ Auth::user()->name }}</div>
                            <div class="text-muted small">{{ now()->translatedFormat('l, d F Y') }}</div>
                            <div class="row g-2 mt-2">
                                <div class="col-6">
                                    <div class="border rounded p-2">
                                        <div class="small text-muted">Masuk</div>
                                        <div class="fs-5 fw-semibold">{{ $todayAttendance?->check_in_time ?? '—' }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded p-2">
                                        <div class="small text-muted">Pulang</div>
                                        <div class="fs-5 fw-semibold">{{ $todayAttendance?->check_out_time ?? '—' }}</div>
                                    </div>
                                </div>
                            </div>
                            @if($todayAttendance)
                                <div class="mt-2">
                                    Status:
                                    <span class="badge rounded-pill
                                        @if($todayAttendance->status === 'hadir') text-bg-success
                                        @elseif($todayAttendance->status === 'terlambat') text-bg-warning
                                        @else text-bg-secondary @endif">
                                        {{ \App\Models\Attendance::STATUS_LABELS[$todayAttendance->status] ?? $todayAttendance->status }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        @if($needScan)
                            <div class="d-grid mt-3">
                                <button type="button" class="btn btn-primary btn-lg fw-semibold"
                                        data-bs-toggle="modal" data-bs-target="#scanModal">
                                    <i class="bi bi-qr-code-scan me-1"></i> Scan QR {{ $modeLabel }}
                                </button>
                            </div>
                        @else
                            <div class="alert alert-success small text-center mt-3 mb-0">
                                Anda sudah menyelesaikan absen masuk dan pulang hari ini. Terima kasih!
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($needScan)
        {{-- Scan QR Modal --}}
        <div class="modal fade" id="scanModal" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content scan-modal">
                    <div class="modal-header scan-header">
                        <div class="scan-head-icon"><i class="bi bi-qr-code-scan"></i></div>
                        <div class="flex-grow-1">
                            <h5 class="modal-title mb-0">Scan QR {{ $modeLabel }}</h5>
                            <div class="scan-subtitle">Arahkan kamera ke QR code</div>
                        </div>
                        <button type="button" class="scan-close" data-bs-dismiss="modal" aria-label="Tutup">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="modal-body p-3">
                        <div class="scan-viewport">
                            <div id="reader"></div>
                            <div class="scan-frame">
                                <span class="corner tl"></span>
                                <span class="corner tr"></span>
                                <span class="corner bl"></span>
                                <span class="corner br"></span>
                                <div class="scan-line"></div>
                            </div>
                            <div class="scan-status" id="scanStatus">
                                <i class="bi bi-qr-code-scan"></i> Menunggu QR Code...
                            </div>
                        </div>

                        <button type="button" id="btnSimulasi" class="btn-simulasi mt-3">
                            <i class="bi bi-magic me-1"></i> Simulasi Scan QR Code
                        </button>

                        <div id="scanResult" class="alert small mt-3 d-none mb-0"></div>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
        <script src="https://unpkg.com/html5-qrcode"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const mode = @json($mode);
                const modalEl = document.getElementById('scanModal');
                const modal = new bootstrap.Modal(modalEl);
                const statusEl = document.getElementById('scanStatus');
                const resultEl = document.getElementById('scanResult');
                const btnSimulasi = document.getElementById('btnSimulasi');
                let html5Qr = null;
                let scanning = false;
                let busy = false;

                const ENDPOINTS = {
                    checkin: @json(route('absensi.checkin')),
                    checkout: @json(route('absensi.checkout')),
                };

                function setStatus(text) {
                    statusEl.innerHTML = '<i class="bi bi-qr-code-scan"></i> ' + text;
                }
                function showResult(message, type) {
                    resultEl.className = 'alert small mt-3 mb-0 ' +
                        (type === 'success' ? 'alert-success' : type === 'error' ? 'alert-danger' : 'alert-info');
                    resultEl.textContent = message;
                }

                async function postJson(url, body) {
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                        credentials: 'same-origin',
                        body: JSON.stringify(body),
                    });
                    const data = await res.json().catch(() => ({}));
                    return { ok: res.ok, data };
                }

                function getPosition() {
                    return new Promise((resolve, reject) => {
                        if (!navigator.geolocation) return reject(new Error('Browser tidak mendukung GPS.'));
                        navigator.geolocation.getCurrentPosition(
                            pos => resolve({ lat: pos.coords.latitude, lng: pos.coords.longitude }),
                            err => reject(new Error('Gagal mengambil lokasi: ' + err.message)),
                            { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
                        );
                    });
                }

                async function stopCamera() {
                    if (html5Qr && scanning) {
                        try { await html5Qr.stop(); } catch (e) {}
                        try { html5Qr.clear(); } catch (e) {}
                    }
                    scanning = false;
                }

                async function finishFlow(token) {
                    if (busy) return;
                    busy = true;
                    await stopCamera();
                    setStatus('QR terdeteksi, memvalidasi...');

                    const scanRes = await postJson(@json(route('absensi.scan')), { token, mode });
                    if (!scanRes.ok) {
                        showResult(scanRes.data.message || 'Token tidak valid.', 'error');
                        busy = false; return;
                    }
                    setStatus('Mengambil lokasi GPS...');
                    let pos;
                    try { pos = await getPosition(); }
                    catch (e) { showResult(e.message, 'error'); busy = false; return; }

                    setStatus('Mengirim data...');
                    const sub = await postJson(ENDPOINTS[mode], { token, latitude: pos.lat, longitude: pos.lng });
                    if (!sub.ok) { showResult(sub.data.message || 'Gagal absen.', 'error'); busy = false; return; }

                    showResult(sub.data.message || 'Absensi berhasil!', 'success');
                    setTimeout(() => window.location.reload(), 1800);
                }

                async function startCamera() {
                    if (typeof Html5Qrcode === 'undefined') {
                        setStatus('Scanner belum siap...');
                        return;
                    }
                    html5Qr = new Html5Qrcode('reader');
                    try {
                        await html5Qr.start(
                            { facingMode: 'environment' },
                            { fps: 10, qrbox: { width: 220, height: 220 } },
                            (decoded) => { if (!scanning) return; finishFlow(decoded); }
                        );
                        scanning = true;
                        setStatus('Menunggu QR Code...');
                    } catch (e) {
                        setStatus('Kamera tidak tersedia — gunakan Simulasi');
                    }
                }

                // Simulasi: rekam absensi tanpa kamera & GPS
                btnSimulasi.addEventListener('click', async () => {
                    if (busy) return;
                    busy = true;
                    btnSimulasi.disabled = true;
                    await stopCamera();
                    setStatus('Memproses simulasi...');
                    const res = await postJson(@json(route('absensi.simulate')), { mode });
                    if (!res.ok) {
                        showResult(res.data.message || 'Simulasi gagal.', 'error');
                        busy = false; btnSimulasi.disabled = false; return;
                    }
                    showResult(res.data.message || 'Simulasi berhasil!', 'success');
                    setTimeout(() => window.location.reload(), 1600);
                });

                modalEl.addEventListener('shown.bs.modal', startCamera);
                modalEl.addEventListener('hidden.bs.modal', stopCamera);

                // Guru langsung diarahkan untuk scan QR
                modal.show();
            });
        </script>
        @endpush
    @endif
</x-app-layout>
