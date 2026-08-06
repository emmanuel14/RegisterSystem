<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendee Pass — <?= htmlspecialchars($reg['registration_code'], ENT_QUOTES, 'UTF-8') ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #fff; color: #1a1a1a; }
        .pass { max-width: 520px; margin: 30px auto; border: 2px solid #1a3c5e; border-radius: 8px; overflow: hidden; }
        .pass-header { background: #1a3c5e; color: #fff; padding: 20px 24px; display: flex; justify-content: space-between; align-items: center; }
        .pass-org { font-size: 18px; font-weight: 700; }
        .pass-event { font-size: 13px; opacity: .8; margin-top: 4px; }
        .pass-body { padding: 24px; display: flex; gap: 24px; }
        .pass-info { flex: 1; }
        .pass-name { font-size: 22px; font-weight: 700; color: #1a3c5e; margin-bottom: 4px; }
        .pass-church { font-size: 13px; color: #555; margin-bottom: 16px; }
        .info-row { display: flex; font-size: 13px; margin-bottom: 6px; }
        .info-row .label { width: 100px; color: #888; flex-shrink: 0; }
        .info-row .value { font-weight: 600; }
        .pass-qr { flex-shrink: 0; text-align: center; }
        .pass-qr img { width: 120px; height: 120px; border: 1px solid #ddd; }
        .pass-qr .code { font-size: 10px; font-family: monospace; margin-top: 6px; color: #555; word-break: break-all; }
        .pass-footer { background: #f5f7fa; border-top: 1px solid #e0e0e0; padding: 12px 24px; font-size: 11px; color: #888; text-align: center; }
        .badge-status { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 700;
                        background: #198754; color: #fff; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
            .pass { margin: 10px auto; box-shadow: none; }
        }
    </style>
</head>
<body>
<?php $e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); ?>

<div class="pass">
    <div class="pass-header">
        <div>
            <div class="pass-org"><?= $e($settings['church_name'] ?? 'EMS') ?></div>
            <div class="pass-event"><?= $e($reg['event_title']) ?></div>
        </div>
        <span class="badge-status">CONFIRMED</span>
    </div>
    <div class="pass-body">
        <div class="pass-info">
            <div class="pass-name"><?= $e($reg['first_name'] . ' ' . $reg['last_name']) ?></div>
            <div class="pass-church"><?= $e($reg['church_name'] ?: '') ?></div>
            <div class="info-row"><span class="label">Code</span><span class="value"><?= $e($reg['registration_code']) ?></span></div>
            <div class="info-row"><span class="label">Email</span><span class="value"><?= $e($reg['email']) ?></span></div>
            <div class="info-row"><span class="label">Phone</span><span class="value"><?= $e($reg['phone']) ?></span></div>
            <div class="info-row"><span class="label">Gender</span><span class="value text-capitalize"><?= $e($reg['gender']) ?></span></div>
            <div class="info-row"><span class="label">State</span><span class="value"><?= $e($reg['state']) ?></span></div>
            <div class="info-row"><span class="label">Event Date</span><span class="value"><?= Helpers\Helper::formatDate($reg['start_date']) ?></span></div>
            <div class="info-row"><span class="label">Venue</span><span class="value"><?= $e($reg['venue'] ?: '—') ?></span></div>
        </div>
        <div class="pass-qr">
            <?php if ($qrUrl): ?>
                <img src="<?= $e($qrUrl) ?>" alt="QR Code">
                <div class="code"><?= $e($reg['registration_code']) ?></div>
            <?php else: ?>
                <div style="width:120px;height:120px;border:1px solid #ddd;display:flex;align-items:center;justify-content:center;font-size:10px;color:#aaa">QR N/A</div>
            <?php endif; ?>
        </div>
    </div>
    <div class="pass-footer">
        Present this pass at the event entrance for check-in.
        Generated on <?= date('M j, Y g:i A') ?>
    </div>
</div>

<div class="no-print" style="text-align:center;margin:20px">
    <button onclick="window.print()" style="padding:8px 24px;background:#1a3c5e;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:14px">
        🖨 Print Pass
    </button>
    <a href="<?= Helpers\Helper::base('registration/download-qr/' . $e($reg['registration_code'])) ?>"
       style="margin-left:10px;padding:8px 24px;background:#c8a456;color:#fff;border:none;border-radius:4px;text-decoration:none;font-size:14px">
        ⬇ Download QR
    </a>
</div>
</body>
</html>
