<x-app-layout>
    <x-slot name="header">Mode Kios Absensi</x-slot>

    @push('styles')
    <style>
        .kiosk-grid { display: grid; grid-template-columns: 1.1fr 1fr; gap: 1.25rem; }
        @media (max-width: 991px) { .kiosk-grid { grid-template-columns: 1fr; } }

        .kiosk-card {
            background: #fff; border-radius: 18px; box-shadow: 0 6px 20px rgba(15,23,41,.06);
            overflow: hidden; border: 1px solid #eef1f5;
        }
        .kiosk-card-header {
            background: linear-gradient(135deg, #15803d, #22c55e); color: #fff;
            padding: .9rem 1.1rem; display: flex; align-items: center; gap: .7rem;
        }
        .kiosk-head-icon {
            width: 40px; height: 40px; border-radius: 10px; background: rgba(255,255,255,.22);
            display: flex; align-items: center; justify-content: center; font-size: 1.2rem;
        }

        .scan-viewport {
            position: relative; width: 100%; aspect-ratio: 4 / 3;
            background: #0f1729; overflow: hidden;
        }
        #reader { position: absolute; inset: 0; }
        #reader video { width: 100% !important; height: 100% !important; object-fit: cover; }
        .scan-frame { position: absolute; inset: 12%; pointer-events: none; }
        .scan-frame .corner { position: absolute; width: 54px; height: 54px; }
        .scan-frame .tl { top: 0; left: 0; border-top: 5px solid #22c55e; border-left: 5px solid #22c55e; border-top-left-radius: 12px; }
        .scan-frame .tr { top: 0; right: 0; border-top: 5px solid #22c55e; border-right: 5px solid #22c55e; border-top-right-radius: 12px; }
        .scan-frame .bl { bottom: 0; left: 0; border-bottom: 5px solid #22c55e; border-left: 5px solid #22c55e; border-bottom-left-radius: 12px; }
        .scan-frame .br { bottom: 0; right: 0; border-bottom: 5px solid #22c55e; border-right: 5px solid #22c55e; border-bottom-right-radius: 12px; }
        .scan-line {
            position: absolute; left: 4%; right: 4%; height: 3px;
            background: linear-gradient(90deg, transparent, #22c55e, transparent);
            box-shadow: 0 0 14px 2px #22c55e; animation: scanmove 2.4s ease-in-out infinite;
        }
        @keyframes scanmove { 0%, 100% { top: 4%; } 50% { top: 93%; } }
        .scan-status {
            position: absolute; left: 50%; bottom: 14px; transform: translateX(-50%);
            background: rgba(15,23,41,.92); color: #dbe4ee; padding: .42rem .95rem;
            border-radius: 999px; font-size: .82rem; display: flex; align-items: center; gap: .4rem;
        }
        .kiosk-controls {
            padding: .85rem 1rem; display: flex; gap: .5rem; align-items: center; flex-wrap: wrap;
            border-top: 1px solid #eef1f5; background: #fafbfc;
        }

        .result-panel { padding: 1.2rem; min-height: 360px; display: flex; flex-direction: column; }
        .result-empty {
            margin: auto; text-align: center; color: #94a3b8;
        }
        .result-empty i { font-size: 3.5rem; display: block; margin-bottom: .5rem; opacity: .35; }
        .result-card {
            border-radius: 14px; padding: 1.1rem; display: flex; gap: 1rem; align-items: center;
            border: 1px solid #e2e8f0;
        }
        .result-card.ok-checkin { background: #ecfdf5; border-color: #86efac; }
        .result-card.ok-checkout { background: #eff6ff; border-color: #93c5fd; }
        .result-card.err { background: #fef2f2; border-color: #fca5a5; }
        .result-photo {
            width: 78px; height: 78px; border-radius: 50%; object-fit: cover;
            background: #e2e8f0 url('{{ asset('img/logo-al-amanah.png') }}') center/60% no-repeat;
            border: 3px solid #fff; box-shadow: 0 2px 6px rgba(0,0,0,.08); flex: 0 0 auto;
        }
        .result-name { font-weight: 700; font-size: 1.1rem; line-height: 1.2; }
        .result-role { color: #64748b; font-size: .82rem; }
        .result-time { font-variant-numeric: tabular-nums; font-weight: 600; font-size: 1.05rem; }
        .badge-status { font-size: .72rem; padding: .35rem .55rem; }
        .recent-list { margin-top: 1rem; max-height: 260px; overflow-y: auto; }
        .recent-row {
            display: flex; gap: .65rem; align-items: center; padding: .55rem .65rem;
            border-radius: 10px; border: 1px solid #eef1f5; margin-bottom: .4rem;
            background: #fff; font-size: .85rem;
        }
        .recent-row .name { font-weight: 600; }
        .recent-row .time { margin-left: auto; color: #475569; font-variant-numeric: tabular-nums; }
    </style>
    @endpush

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-0">Mode Kios Absensi</h1>
            <small class="text-muted">
                Operator: <strong>{{ $operator->name }}</strong> ({{ $operator->roleLabel() }}) &middot;
                Sekolah: <strong>{{ $settings->name ?? '—' }}</strong>
            </small>
        </div>
        <span class="badge text-bg-success-subtle text-success border border-success-subtle">
            <i class="bi bi-broadcast"></i> Kios aktif
        </span>
    </div>

    <div class="kiosk-grid">
        {{-- Kolom kiri: kamera --}}
        <div class="kiosk-card">
            <div class="kiosk-card-header">
                <div class="kiosk-head-icon"><i class="bi bi-camera-video-fill"></i></div>
                <div class="flex-grow-1">
                    <div class="fw-semibold">Kamera Scanner</div>
                    <small style="opacity:.9">Arahkan kartu QR guru ke kamera</small>
                </div>
            </div>
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
                    <i class="bi bi-qr-code-scan"></i> Menyiapkan kamera...
                </div>
            </div>
            <div class="kiosk-controls">
                <button type="button" id="btnStart" class="btn btn-success btn-sm">
                    <i class="bi bi-play-fill"></i> Mulai
                </button>
                <button type="button" id="btnStop" class="btn btn-outline-secondary btn-sm" disabled>
                    <i class="bi bi-pause-fill"></i> Jeda
                </button>
                <select id="cameraSelect" class="form-select form-select-sm" style="max-width: 260px;"></select>
                <span class="text-muted small ms-auto" id="lastScanInfo">Belum ada scan</span>
            </div>
        </div>

        {{-- Kolom kanan: hasil scan --}}
        <div class="kiosk-card">
            <div class="kiosk-card-header" style="background: linear-gradient(135deg, #1e40af, #3b82f6);">
                <div class="kiosk-head-icon"><i class="bi bi-person-check-fill"></i></div>
                <div class="flex-grow-1">
                    <div class="fw-semibold">Hasil Scan</div>
                    <small style="opacity:.9">{{ now()->translatedFormat('l, d F Y') }}</small>
                </div>
            </div>
            <div class="result-panel">
                <div id="resultEmpty" class="result-empty">
                    <i class="bi bi-qr-code"></i>
                    Menunggu scan QR pertama...
                </div>
                <div id="resultBox" class="d-none"></div>

                <div class="recent-list" id="recentList"></div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const SCAN_URL = @json(route('admin.kiosk.scan'));

            const statusEl = document.getElementById('scanStatus');
            const resultEmpty = document.getElementById('resultEmpty');
            const resultBox = document.getElementById('resultBox');
            const recentList = document.getElementById('recentList');
            const lastScanInfo = document.getElementById('lastScanInfo');
            const btnStart = document.getElementById('btnStart');
            const btnStop = document.getElementById('btnStop');
            const cameraSelect = document.getElementById('cameraSelect');

            let html5Qr = null;
            let scanning = false;
            let busy = false;
            let lastPayload = null;
            let lastPayloadTime = 0;

            function setStatus(text) {
                statusEl.innerHTML = '<i class="bi bi-qr-code-scan"></i> ' + text;
            }

            function escapeHtml(s) {
                return String(s ?? '').replace(/[&<>"']/g, c =>
                    ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
            }

            function renderResult(payload, ok, mode) {
                const u = payload.user || {};
                const a = payload.attendance || {};
                const photo = u.photo_url
                    ? `<img src="${escapeHtml(u.photo_url)}" class="result-photo" alt="">`
                    : `<div class="result-photo"></div>`;

                let cls = ok ? (mode === 'checkout' ? 'ok-checkout' : 'ok-checkin') : 'err';
                let timeText = '';
                let badge = '';
                if (ok && mode === 'checkin') {
                    timeText = `<div class="result-time">${escapeHtml(a.check_in_time || '')}</div>`;
                    badge = `<span class="badge ${a.status === 'terlambat' ? 'text-bg-warning' : 'text-bg-success'} badge-status">${escapeHtml((a.status_label || '').toUpperCase())}</span>`;
                } else if (ok && mode === 'checkout') {
                    timeText = `<div class="result-time">${escapeHtml(a.check_out_time || '')}</div>`;
                    badge = `<span class="badge text-bg-primary badge-status">PULANG</span>`;
                } else {
                    badge = `<span class="badge text-bg-danger badge-status">GAGAL</span>`;
                }

                resultBox.className = '';
                resultBox.innerHTML = `
                    <div class="result-card ${cls}">
                        ${photo}
                        <div class="flex-grow-1">
                            <div class="result-name">${escapeHtml(u.name || 'Tidak dikenal')}</div>
                            <div class="result-role">${escapeHtml(u.role_label || '')}${u.nip ? ' &middot; NIP ' + escapeHtml(u.nip) : ''}</div>
                            <div class="mt-1">${badge} <small class="text-muted ms-1">${escapeHtml(payload.message || '')}</small></div>
                        </div>
                        ${timeText}
                    </div>
                `;
                resultEmpty.classList.add('d-none');
                resultBox.classList.remove('d-none');
            }

            function pushRecent(payload, ok, mode) {
                if (!ok) return;
                const u = payload.user || {};
                const a = payload.attendance || {};
                const time = mode === 'checkout' ? a.check_out_time : a.check_in_time;
                const label = mode === 'checkout' ? 'Pulang' : 'Masuk';
                const cls = mode === 'checkout' ? 'text-primary' : 'text-success';
                const row = document.createElement('div');
                row.className = 'recent-row';
                row.innerHTML = `
                    <i class="bi bi-check-circle-fill ${cls}"></i>
                    <span class="name">${escapeHtml(u.name || '')}</span>
                    <span class="text-muted">${label}</span>
                    <span class="time">${escapeHtml(time || '')}</span>
                `;
                recentList.prepend(row);
                while (recentList.childElementCount > 8) {
                    recentList.removeChild(recentList.lastChild);
                }
            }

            async function postJson(url, body) {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                    credentials: 'same-origin',
                    body: JSON.stringify(body),
                });
                const data = await res.json().catch(() => ({}));
                return { ok: res.ok, status: res.status, data };
            }

            function beep(ok) {
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    const o = ctx.createOscillator();
                    const g = ctx.createGain();
                    o.frequency.value = ok ? 880 : 320;
                    o.connect(g); g.connect(ctx.destination);
                    g.gain.setValueAtTime(0.0001, ctx.currentTime);
                    g.gain.exponentialRampToValueAtTime(0.2, ctx.currentTime + 0.02);
                    g.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.22);
                    o.start(); o.stop(ctx.currentTime + 0.25);
                } catch (e) {}
            }

            async function handleDecoded(payloadStr) {
                if (busy) return;
                const now = Date.now();
                // Cooldown 2.5s: cegah scan ganda kartu yang sama
                if (payloadStr === lastPayload && (now - lastPayloadTime) < 2500) return;
                lastPayload = payloadStr;
                lastPayloadTime = now;

                busy = true;
                setStatus('Memproses ' + payloadStr.substring(0, 24) + '...');

                const res = await postJson(SCAN_URL, { payload: payloadStr });
                if (res.ok && res.data.ok) {
                    renderResult(res.data, true, res.data.mode);
                    pushRecent(res.data, true, res.data.mode);
                    beep(true);
                    lastScanInfo.textContent = (res.data.user?.name || '') + ' — ' + new Date().toLocaleTimeString();
                    setStatus('Berhasil — siap scan berikutnya');
                } else {
                    renderResult(res.data, false, null);
                    beep(false);
                    setStatus(res.data.message || 'Gagal — siap scan ulang');
                }

                // Cooldown sebelum siap scan berikutnya
                setTimeout(() => { busy = false; }, 1500);
            }

            async function startCamera(deviceId) {
                if (typeof Html5Qrcode === 'undefined') {
                    setStatus('Library scanner gagal dimuat');
                    return;
                }
                if (scanning) await stopCamera();
                html5Qr = new Html5Qrcode('reader');
                const cameraConfig = deviceId ? { deviceId: { exact: deviceId } } : { facingMode: 'user' };
                try {
                    await html5Qr.start(
                        cameraConfig,
                        { fps: 12, qrbox: { width: 280, height: 280 } },
                        (decoded) => { handleDecoded(decoded); }
                    );
                    scanning = true;
                    btnStart.disabled = true;
                    btnStop.disabled = false;
                    setStatus('Menunggu QR code...');
                } catch (e) {
                    setStatus('Kamera tidak tersedia: ' + (e.message || e));
                }
            }

            async function stopCamera() {
                if (html5Qr && scanning) {
                    try { await html5Qr.stop(); } catch (e) {}
                    try { html5Qr.clear(); } catch (e) {}
                }
                scanning = false;
                btnStart.disabled = false;
                btnStop.disabled = true;
                setStatus('Kamera dijeda');
            }

            async function loadCameras() {
                try {
                    const cams = await Html5Qrcode.getCameras();
                    cameraSelect.innerHTML = '';
                    cams.forEach(c => {
                        const opt = document.createElement('option');
                        opt.value = c.id;
                        opt.textContent = c.label || `Kamera ${c.id.substring(0, 6)}`;
                        cameraSelect.appendChild(opt);
                    });
                    if (cams.length === 0) {
                        cameraSelect.innerHTML = '<option>Tidak ada kamera</option>';
                    }
                } catch (e) {
                    cameraSelect.innerHTML = '<option>Akses kamera ditolak</option>';
                }
            }

            btnStart.addEventListener('click', () => startCamera(cameraSelect.value || null));
            btnStop.addEventListener('click', stopCamera);
            cameraSelect.addEventListener('change', () => { if (scanning) startCamera(cameraSelect.value); });

            (async () => {
                await loadCameras();
                await startCamera(cameraSelect.value || null);
            })();
        });
    </script>
    @endpush
</x-app-layout>
