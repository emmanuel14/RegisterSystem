<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width">
<title>Registration Confirmation</title>
<style>
  body { margin:0; padding:0; background:#f5f7fa; font-family: 'Segoe UI', Arial, sans-serif; color:#1a1a1a; }
  .wrapper { max-width:560px; margin:30px auto; }
  .header { background:#1a3c5e; padding:30px 32px; border-radius:8px 8px 0 0; text-align:center; }
  .header h1 { color:#fff; margin:0; font-size:22px; font-weight:700; }
  .header p { color:rgba(255,255,255,0.75); margin:6px 0 0; font-size:13px; }
  .body { background:#fff; padding:32px; border-left:1px solid #e0e0e0; border-right:1px solid #e0e0e0; }
  .success-badge { text-align:center; margin-bottom:24px; }
  .success-badge span { display:inline-block; background:#d4edda; color:#155724; padding:8px 24px; border-radius:20px; font-weight:700; font-size:14px; }
  .code-box { background:#f8f9fa; border:2px dashed #c8a456; border-radius:8px; padding:16px; text-align:center; margin:20px 0; }
  .code-box .code { font-size:22px; font-weight:800; color:#1a3c5e; font-family:monospace; letter-spacing:1px; }
  .code-box .label { font-size:11px; color:#888; text-transform:uppercase; letter-spacing:1px; margin-bottom:4px; }
  dl { margin:0; }
  dt { font-size:11px; color:#888; text-transform:uppercase; letter-spacing:.5px; margin-top:12px; }
  dd { margin:2px 0 0; font-weight:600; font-size:14px; }
  .qr-section { text-align:center; padding:24px 0; border-top:1px solid #f0f0f0; border-bottom:1px solid #f0f0f0; margin:24px 0; }
  .qr-section img { width:160px; height:160px; border:1px solid #e0e0e0; }
  .qr-note { font-size:12px; color:#888; margin-top:8px; }
  .btn-primary { display:inline-block; background:#1a3c5e; color:#fff !important; text-decoration:none; padding:12px 28px; border-radius:6px; font-weight:600; font-size:14px; }
  .footer { background:#f5f7fa; border:1px solid #e0e0e0; border-top:none; border-radius:0 0 8px 8px; padding:20px 32px; text-align:center; font-size:12px; color:#888; }
</style>
</head>
<body>
<?php
$e    = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$site = rtrim($settings['site_url'] ?? 'http://localhost', '/');
$org  = $settings['church_name'] ?? 'Event Management System';
?>
<div class="wrapper">
  <div class="header">
    <h1><?= $e($org) ?></h1>
    <p>Event Registration System</p>
  </div>

  <div class="body">
    <div class="success-badge"><span>✓ Registration Confirmed</span></div>

    <p style="font-size:15px">Dear <strong><?= $e($registration['first_name']) ?></strong>,</p>
    <p style="font-size:14px;color:#555">Thank you for registering! Your registration has been confirmed. Please keep this email for your records and present the QR code at the event entrance.</p>

    <div class="code-box">
      <div class="label">Registration Number</div>
      <div class="code"><?= $e($registration['registration_code']) ?></div>
    </div>

    <dl>
      <dt>Event</dt>       <dd><?= $e($registration['event_title']) ?></dd>
      <dt>Attendee</dt>    <dd><?= $e($registration['first_name'] . ' ' . $registration['last_name']) ?></dd>
      <dt>Email</dt>       <dd><?= $e($registration['email']) ?></dd>
      <dt>Date</dt>        <dd><?= Helpers\Helper::formatDate($registration['start_date']) ?></dd>
      <?php if ($registration['venue']): ?>
      <dt>Venue</dt>       <dd><?= $e($registration['venue']) ?></dd>
      <?php endif; ?>
      <dt>Church</dt>      <dd><?= $e($registration['church_name'] ?: '—') ?></dd>
    </dl>

    <div class="qr-section">
      <p style="font-size:13px;color:#555;margin-bottom:12px">Present this QR code at the event entrance:</p>
      <img src="<?= $e(\Helpers\QRCode::url(\Helpers\QRCode::filename($registration['registration_code']))) ?>"
           alt="QR Code" style="display:block;margin:0 auto">
      <div class="qr-note">Or scan: <?= $e($site . '/checkin/' . $registration['registration_code']) ?></div>
    </div>

    <div style="text-align:center;margin-top:24px">
      <a href="<?= $e($site . '/registration/success/' . $registration['registration_code']) ?>" class="btn-primary">
        View Registration &rarr;
      </a>
    </div>
  </div>

  <div class="footer">
    &copy; <?= date('Y') ?> <?= $e($org) ?>. This is an automated email — please do not reply.<br>
    If you didn't register for this event, please ignore this email.
  </div>
</div>
</body>
</html>
