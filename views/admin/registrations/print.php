<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendee Pass — <?= htmlspecialchars($reg['registration_code'], ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', system-ui, sans-serif; background: #f1f4f9; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px; }
        .pass { width: 540px; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,.15); }
        .pass-header { background: linear-gradient(135deg, #1e3a5f, #274d80); padding: 24px 28px; display: flex; align-items: center; justify-content: space-between; }
        .pass-org { color: #fff; font-size: 17px; font-weight: 800; }
        .pass-event { color: rgba(255,255,255,.65); font-size: 12.5px; margin-top: 3px; }
        .pass-confirmed { background: rgba(16,185,129,.2); border: 1px solid rgba(16,185,129,.3); color: #6ee7b7; padding: 5px 14px; border-radius: 20px; font-size: 11px; font-weight: 700; letter-spacing: .4px; }
        .pass-body { padding: 28px; display: flex; gap: 24px; }
        .pass-info { flex: 1; }
        .pass-name { font-size: 22px; font-weight: 800; color: #0f172a; letter-spacing: -.3px; margin-bottom: 3px; }
        .pass-church { font-size: 13px; color: #64748b; margin-bottom: 18px; }
        .info-row { display: flex; gap: 10px; margin-bottom: 8px; align-items: flex-start; }
        .info-icon { width: 28px; height: 28px; border-radius: 7px; background: #e8f0f9; display: flex; align-items: center; justify-content: center; color: #1e3a5f; font-size: 12px; flex-shrink: 0; }
        .info-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #94a3b8; }
        .info-value { font-size: 13px; font-weight: 600; color: #0f172a; margin-top: 1px; }
        .pass-qr { flex-shrink: 0; text-align: center; }
        .pass-qr img { width: 130px; height: 130px; border: 3px solid #e2e8f0; border-radius: 12px; padding: 6px; }
        .pass-code { font-family: monospace; font-size: 10px; background: #e8f0f9; color: #1e3a5f; padding: 4px 8px; border-radius: 6px; margin-top: 8px; display: inline-block; font-weight: 700; }
        .pass-footer { background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 14px 28px; display: flex; align-items: center; justify-content: space-between; }
        .pass-footer-left { font-size: 11px; color: #94a3b8; }
        .pass-watermark { font-size: 10px; color: #cbd5e1; font-weight: 600; letter-spacing: .5px; }
        .no-print { text-align: center; padding: 20px; display: flex; gap: 10px; justify-content: center; }
        .no-print button, .no-print a { padding: 10px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-print { background: #1e3a5f; color: #fff; }
        .btn-download { background: #c9963a; color: #fff; }
        @media print {
            body { background: #fff; padding: 0; }
            .pass { box-shadow: none; border-radius: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
<?php $e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); ?>
<?php $B = fn(string $p = '') => Helpers\Helper::base($p); ?>

<div>
    <div class="pass">
        <div class="pass-header">
            <div>
                <div class="pass-org"><?= $e($settings['church_name'] ?? 'EMS') ?></div>
                <div class="pass-event"><?= $e($reg['event_title']) ?></div>
            </div>
            <div class="pass-confirmed">✓ CONFIRMED</div>
        </div>

        <div class="pass-body">
            <div class="pass-info">
                <div class="pass-name"><?= $e($reg['first_name'].' '.$reg['last_name']) ?></div>
                <div class="pass-church"><?= $e($reg['church_name'] ?: '') ?></div>

                <div class="info-row">
                    <div class="info-icon">✉</div>
                    <div><div class="info-label">Email</div><div class="info-value"><?= $e($reg['email']) ?></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon">📞</div>
                    <div><div class="info-label">Phone</div><div class="info-value"><?= $e($reg['phone']) ?></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon">📅</div>
                    <div><div class="info-label">Event Date</div><div class="info-value"><?= Helpers\Helper::formatDate($reg['start_date']) ?></div></div>
                </div>
                <?php if ($reg['venue']): ?>
                <div class="info-row">
                    <div class="info-icon">📍</div>
                    <div><div class="info-label">Venue</div><div class="info-value"><?= $e($reg['venue']) ?></div></div>
                </div>
                <?php endif; ?>
                <div class="info-row">
                    <div class="info-icon">👤</div>
                    <div><div class="info-label">Gender</div><div class="info-value" style="text-transform:capitalize"><?= $e($reg['gender']) ?></div></div>
                </div>
                <div class="info-row">
                    <div class="info-icon">🗺</div>
                    <div><div class="info-label">State</div><div class="info-value"><?= $e($reg['state'] ?: '—') ?></div></div>
                </div>
            </div>

            <div class="pass-qr">
                <?php if ($qrUrl): ?>
                    <img src="<?= $e($qrUrl) ?>" alt="QR Code">
                    <div class="pass-code"><?= $e($reg['registration_code']) ?></div>
                <?php else: ?>
                    <div style="width:130px;height:130px;border:3px solid #e2e8f0;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:11px;color:#94a3b8">QR N/A</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="pass-footer">
            <div class="pass-footer-left">Present this pass at the event entrance for check-in</div>
            <div class="pass-watermark">EMS PLATFORM</div>
        </div>
    </div>

    <div class="no-print">
        <button onclick="window.print()" class="btn-print">🖨 Print Pass</button>
        <a href="<?= $B('registration/download-qr/'.$e($reg['registration_code'])) ?>" class="btn-download">⬇ Download QR</a>
    </div>
</div>
</body>
</html>
