<?php
$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$pageTitle = 'QR Check-In';
?>

<div class="page-header">
    <div>
        <h2 class="page-title"><i class="bi bi-qr-code-scan me-2"></i>QR Check-In</h2>
        <p class="page-subtitle">Scan attendee QR codes or search by registration code</p>
    </div>
</div>

<div class="row g-4">
    <!-- Scanner -->
    <div class="col-lg-6">
        <div class="card ems-card">
            <div class="card-header">
                <h5 class="card-title mb-0">Camera Scanner</h5>
            </div>
            <div class="card-body">
                <div id="qr-reader" class="w-100 rounded overflow-hidden mb-3" style="min-height:280px;background:#000"></div>
                <div class="d-flex gap-2">
                    <button id="startScan" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-camera-video me-1"></i> Start Camera
                    </button>
                    <button id="stopScan" class="btn btn-outline-secondary" style="display:none">
                        <i class="bi bi-stop-circle me-1"></i> Stop
                    </button>
                </div>
            </div>
        </div>

        <!-- Manual Lookup -->
        <div class="card ems-card mt-4">
            <div class="card-header"><h5 class="card-title mb-0">Manual Lookup</h5></div>
            <div class="card-body">
                <div class="input-group">
                    <input type="text" id="manualCode" class="form-control" placeholder="Enter registration code e.g. EMS-2026-000001"
                           autocomplete="off" autocapitalize="off">
                    <button class="btn btn-primary" id="manualLookup">
                        <i class="bi bi-search"></i> Look Up
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Result Panel -->
    <div class="col-lg-6">
        <div class="card ems-card h-100" id="resultPanel">
            <div class="card-header"><h5 class="card-title mb-0">Scan Result</h5></div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center text-center" id="resultBody">
                <i class="bi bi-qr-code fs-1 text-muted mb-3"></i>
                <p class="text-muted">Scan a QR code or enter a registration code to see attendee details.</p>
            </div>
        </div>
    </div>
</div>

<!-- html5-qrcode -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
const CSRF        = '<?= $e($csrf) ?>';
const CSRF_NAME   = '<?= CSRF_TOKEN_NAME ?>';
let html5QrCode   = null;
let scanning      = false;
let lastCode      = '';

// ── QR Scanner ────────────────────────────────────────────────────────────
document.getElementById('startScan').addEventListener('click', startScanner);
document.getElementById('stopScan').addEventListener('click', stopScanner);

function startScanner() {
    html5QrCode = new Html5Qrcode('qr-reader');
    Html5Qrcode.getCameras().then(devices => {
        if (!devices.length) {
            alert('No cameras found on this device.');
            return;
        }
        const cameraId = devices[devices.length - 1].id; // prefer rear
        html5QrCode.start(
            cameraId,
            { fps: 10, qrbox: { width: 250, height: 250 } },
            onScanSuccess,
            () => {}
        ).then(() => {
            document.getElementById('startScan').style.display = 'none';
            document.getElementById('stopScan').style.display  = 'inline-block';
            scanning = true;
        }).catch(err => alert('Camera error: ' + err));
    }).catch(err => alert('Cannot access cameras: ' + err));
}

function stopScanner() {
    html5QrCode?.stop().then(() => {
        document.getElementById('startScan').style.display = 'inline-block';
        document.getElementById('stopScan').style.display  = 'none';
        scanning = false;
    });
}

async function onScanSuccess(decodedText) {
    if (decodedText === lastCode) return;
    lastCode = decodedText;
    await lookupCode(decodedText);
    // Brief pause before allowing re-scan
    setTimeout(() => { lastCode = ''; }, 3000);
}

// ── Manual Lookup ─────────────────────────────────────────────────────────
document.getElementById('manualLookup').addEventListener('click', () => {
    const code = document.getElementById('manualCode').value.trim();
    if (code) lookupCode(code);
});
document.getElementById('manualCode').addEventListener('keypress', e => {
    if (e.key === 'Enter') {
        const code = e.target.value.trim();
        if (code) lookupCode(code);
    }
});

// ── Lookup ────────────────────────────────────────────────────────────────
async function lookupCode(code) {
    showLoading();

    const res  = await fetch('/admin/checkin/lookup', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `${CSRF_NAME}=${CSRF}&code=${encodeURIComponent(code)}`
    });
    const data = await res.json();

    if (!data.success) {
        showError(data.message || 'Not found');
        return;
    }

    showResult(data.registration);
}

function showLoading() {
    document.getElementById('resultBody').innerHTML = `
        <div class="spinner-border text-primary" role="status"></div>
        <p class="text-muted mt-3">Looking up…</p>`;
}

function showError(msg) {
    document.getElementById('resultBody').innerHTML = `
        <i class="bi bi-x-circle-fill text-danger fs-1 mb-3"></i>
        <p class="text-danger fw-semibold">${escHtml(msg)}</p>`;
}

function showResult(reg) {
    const checkedIn  = reg.checked_in;
    const statusHtml = checkedIn
        ? `<div class="alert alert-warning mb-3"><i class="bi bi-exclamation-triangle me-1"></i>Already checked in at ${escHtml(reg.checked_in_at || '')}</div>`
        : '';

    const checkinBtn = checkedIn ? '' : `
        <button class="btn btn-success btn-lg mt-3 w-100" onclick="doCheckin(${reg.id})">
            <i class="bi bi-patch-check me-2"></i>Check In
        </button>`;

    document.getElementById('resultBody').innerHTML = `
        <div class="text-start w-100">
            ${statusHtml}
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="avatar-circle avatar-circle--lg">${initials(reg.attendee_name)}</div>
                <div>
                    <div class="fs-5 fw-bold">${escHtml(reg.attendee_name)}</div>
                    <code class="small">${escHtml(reg.registration_code)}</code>
                </div>
            </div>
            <dl class="info-list mb-0">
                <dt>Event</dt><dd>${escHtml(reg.event_title)}</dd>
                <dt>Email</dt><dd>${escHtml(reg.email)}</dd>
                <dt>Phone</dt><dd>${escHtml(reg.phone)}</dd>
                <dt>Church</dt><dd>${escHtml(reg.church_name || '—')}</dd>
                <dt>Status</dt><dd><span class="badge bg-success">${escHtml(reg.status)}</span></dd>
            </dl>
            ${checkinBtn}
        </div>`;
}

async function doCheckin(id) {
    const btn  = document.querySelector('.btn-success[onclick]');
    if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Checking in…'; }

    const res  = await fetch(`/admin/checkin/${id}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `${CSRF_NAME}=${CSRF}`
    });
    const data = await res.json();

    if (data.success) {
        Swal.fire({ icon: 'success', title: 'Checked In!', text: 'Attendee checked in successfully.', timer: 2000, showConfirmButton: false });
        document.getElementById('resultBody').innerHTML += `<div class="alert alert-success mt-3"><i class="bi bi-check-circle me-1"></i>Checked in at ${data.checked_in_at}</div>`;
    } else if (data.already_in) {
        Swal.fire({ icon: 'info', title: 'Already Checked In', text: data.message });
    } else {
        Swal.fire({ icon: 'error', title: 'Error', text: data.message });
    }
}

function initials(name) {
    return name.split(' ').map(w => w[0]).join('').substring(0,2).toUpperCase();
}

function escHtml(str) {
    return String(str||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
