<?php
$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$pageTitle = 'QR Check-In';
$B = fn(string $p = '') => Helpers\Helper::base($p);
?>

<div class="page-header">
    <div>
        <h1 class="page-title">QR Check-In</h1>
        <p class="page-subtitle">Scan attendee QR codes or look up by registration number</p>
    </div>
</div>

<div class="row g-4">
    <!-- Scanner Panel -->
    <div class="col-lg-5">
        <div class="ems-card mb-4">
            <div class="card-header">
                <div class="card-title"><i class="bi bi-camera me-2" style="color:var(--ems-gold)"></i>Camera Scanner</div>
            </div>
            <div class="card-body">
                <div id="qr-reader" style="width:100%;min-height:260px;background:#0d1b2e;border-radius:10px;overflow:hidden;margin-bottom:14px;display:flex;align-items:center;justify-content:center">
                    <div style="text-align:center;color:rgba(255,255,255,.4)">
                        <i class="bi bi-camera-video" style="font-size:36px;display:block;margin-bottom:8px"></i>
                        <span style="font-size:13px">Camera preview</span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button id="startScan" class="btn btn-primary flex-grow-1"><i class="bi bi-camera-video me-1"></i>Start Camera</button>
                    <button id="stopScan"  class="btn btn-outline-secondary" style="display:none"><i class="bi bi-stop-circle me-1"></i>Stop</button>
                </div>
            </div>
        </div>

        <div class="ems-card">
            <div class="card-header">
                <div class="card-title"><i class="bi bi-search me-2" style="color:var(--ems-gold)"></i>Manual Lookup</div>
            </div>
            <div class="card-body">
                <div class="input-group">
                    <input type="text" id="manualCode" class="form-control" placeholder="EMS-2026-000001" autocomplete="off" style="font-family:monospace">
                    <button class="btn btn-primary" id="manualLookup"><i class="bi bi-search"></i></button>
                </div>
                <div class="form-text">Enter full registration code or scan URL</div>
            </div>
        </div>
    </div>

    <!-- Result Panel -->
    <div class="col-lg-7">
        <div class="ems-card h-100">
            <div class="card-header">
                <div class="card-title"><i class="bi bi-person-check me-2" style="color:var(--ems-gold)"></i>Attendee</div>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center text-center" id="resultBody" style="min-height:360px">
                <div style="width:72px;height:72px;border-radius:18px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:30px;color:#94a3b8;margin-bottom:16px">
                    <i class="bi bi-qr-code"></i>
                </div>
                <div style="font-size:16px;font-weight:700;color:var(--text-primary);margin-bottom:6px">Ready to Scan</div>
                <div style="font-size:13.5px;color:var(--text-secondary)">Start the camera or enter a registration code to look up an attendee.</div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
const CSRF      = '<?= $e($csrf) ?>';
const BASE      = '<?= $B() ?>';
let html5QrCode = null;
let lastCode    = '';

document.getElementById('startScan').addEventListener('click', startScanner);
document.getElementById('stopScan').addEventListener('click', stopScanner);

function startScanner() {
    html5QrCode = new Html5Qrcode('qr-reader');
    Html5Qrcode.getCameras().then(devices => {
        if (!devices.length) { alert('No camera found.'); return; }
        const cam = devices[devices.length - 1].id;
        html5QrCode.start(cam, {fps:10, qrbox:{width:240,height:240}}, onScanSuccess, ()=>{})
            .then(() => {
                document.getElementById('startScan').style.display = 'none';
                document.getElementById('stopScan').style.display  = 'inline-flex';
            }).catch(err => alert('Camera error: ' + err));
    }).catch(err => alert('Cannot access cameras: ' + err));
}

function stopScanner() {
    html5QrCode?.stop().then(() => {
        document.getElementById('startScan').style.display = 'inline-flex';
        document.getElementById('stopScan').style.display  = 'none';
    });
}

async function onScanSuccess(text) {
    if (text === lastCode) return;
    lastCode = text;
    await lookupCode(text);
    setTimeout(() => { lastCode = ''; }, 3000);
}

document.getElementById('manualLookup').addEventListener('click', () => {
    const c = document.getElementById('manualCode').value.trim();
    if (c) lookupCode(c);
});
document.getElementById('manualCode').addEventListener('keypress', e => {
    if (e.key === 'Enter') { const c = e.target.value.trim(); if (c) lookupCode(c); }
});

async function lookupCode(code) {
    showLoading();
    const res  = await fetch(BASE + 'admin/checkin/lookup', {
        method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'<?= CSRF_TOKEN_NAME ?>='+CSRF+'&code='+encodeURIComponent(code)
    });
    const data = await res.json();
    if (!data.success) { showError(data.message || 'Registration not found'); return; }
    showResult(data.registration);
}

function showLoading() {
    document.getElementById('resultBody').innerHTML = `
        <div class="spinner-border text-primary" role="status" style="width:36px;height:36px"></div>
        <div style="margin-top:14px;color:var(--text-secondary);font-size:13.5px">Looking up…</div>`;
}

function showError(msg) {
    document.getElementById('resultBody').innerHTML = `
        <div style="width:64px;height:64px;border-radius:16px;background:#fef2f2;display:flex;align-items:center;justify-content:center;font-size:28px;color:#ef4444;margin-bottom:14px">
            <i class="bi bi-x-circle"></i>
        </div>
        <div style="font-size:15px;font-weight:700;color:#991b1b;margin-bottom:6px">Not Found</div>
        <div style="font-size:13.5px;color:#b91c1c">${escH(msg)}</div>`;
}

function showResult(reg) {
    const ci = reg.checked_in;
    const rb = document.getElementById('resultBody');
    rb.className = 'card-body';
    rb.style = '';

    const iconColor  = ci ? '#f59e0b' : '#10b981';
    const iconBg     = ci ? '#fffbeb' : '#ecfdf5';
    const iconClass  = ci ? 'bi-exclamation-circle-fill' : 'bi-check-circle-fill';
    const statusText = ci ? 'Already Checked In' : 'Ready to Check In';

    rb.innerHTML = `
    <div style="padding:8px 0">
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid #f0f4f8">
            <div style="width:52px;height:52px;border-radius:50%;background:linear-gradient(135deg,#1e3a5f,#274d80);display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;color:#fff;flex-shrink:0">
                ${initials(reg.attendee_name)}
            </div>
            <div style="text-align:left">
                <div style="font-size:17px;font-weight:800;color:var(--text-primary)">${escH(reg.attendee_name)}</div>
                <code style="font-size:12px;background:#e8f0f9;color:#1e3a5f;padding:2px 8px;border-radius:5px">${escH(reg.registration_code)}</code>
            </div>
            <div style="margin-left:auto;text-align:right">
                <div style="display:inline-flex;align-items:center;gap:6px;background:${iconBg};color:${iconColor};padding:5px 12px;border-radius:20px;font-size:12px;font-weight:700">
                    <i class="bi ${iconClass}"></i> ${escH(statusText)}
                </div>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;text-align:left">
            ${infoBox('bi-calendar3','Event',reg.event_title)}
            ${infoBox('bi-envelope','Email',reg.email)}
            ${infoBox('bi-telephone','Phone',reg.phone)}
            ${infoBox('bi-building','Church',reg.church_name||'—')}
        </div>

        ${!ci ? `<button onclick="doCheckin(${reg.id})" class="btn btn-success w-100 btn-lg" style="border-radius:10px;font-size:15px;font-weight:700;padding:14px">
            <i class="bi bi-patch-check me-2"></i>Check In Now
        </button>` : `<div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:14px;text-align:center;font-size:13.5px;color:#92400e;font-weight:600">
            <i class="bi bi-clock me-1"></i>Checked in at ${escH(reg.checked_in_at||'')}
        </div>`}
    </div>`;
}

function infoBox(icon, label, value) {
    return `<div style="background:#f8fafc;border-radius:10px;padding:12px">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--text-muted);margin-bottom:4px">
            <i class="bi ${icon} me-1"></i>${escH(label)}
        </div>
        <div style="font-size:13px;font-weight:600;color:var(--text-primary);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${escH(String(value||''))}</div>
    </div>`;
}

async function doCheckin(id) {
    const btn = document.querySelector('.btn-success[onclick]');
    if (btn) { btn.disabled=true; btn.innerHTML='<span class="spinner-border spinner-border-sm me-2"></span>Checking in…'; }
    const res  = await fetch(BASE + 'admin/checkin/' + id, {
        method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'<?= CSRF_TOKEN_NAME ?>='+CSRF
    });
    const data = await res.json();
    if (data.success) {
        Swal.fire({icon:'success',title:'Checked In!',text:'Attendee checked in successfully.',timer:2000,showConfirmButton:false});
        setTimeout(() => document.getElementById('manualCode') && lookupCode(document.getElementById('manualCode').value), 2100);
    } else {
        Swal.fire({icon:data.already_in?'info':'error', title:data.already_in?'Already Checked In':'Error', text:data.message});
    }
}

function initials(name) { return String(name||'').split(' ').map(w=>w[0]||'').join('').substring(0,2).toUpperCase(); }
function escH(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
</script>
