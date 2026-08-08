<?php
$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$pageTitle = 'Event Check-In';
$B = fn(string $p = '') => Helpers\Helper::base($p);
?>

<div style="min-height:80vh;background:linear-gradient(160deg,#0d1b2e,#1a3c5e);display:flex;align-items:center;justify-content:center;padding:40px 16px">
    <div style="width:100%;max-width:460px">
        <?php if (!$reg): ?>
            <div class="checkin-card" style="background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.3)">
                <div style="background:#fef2f2;padding:32px;text-align:center;border-bottom:1px solid #fecaca">
                    <div style="width:72px;height:72px;border-radius:50%;background:#fee2e2;border:2px solid #fecaca;display:flex;align-items:center;justify-content:center;font-size:30px;color:#ef4444;margin:0 auto 16px">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <h2 style="font-size:20px;font-weight:800;color:#991b1b;margin-bottom:6px">Invalid QR Code</h2>
                    <p style="font-size:13.5px;color:#b91c1c;margin:0">This registration code was not found in our system.</p>
                </div>
                <div style="padding:24px;text-align:center">
                    <p style="font-size:13.5px;color:#64748b;margin-bottom:20px">If you believe this is an error, please contact the event organiser with your registration number.</p>
                    <a href="<?= $B() ?>" style="display:inline-flex;align-items:center;gap:8px;background:#1e3a5f;color:#fff;padding:10px 24px;border-radius:8px;text-decoration:none;font-size:13.5px;font-weight:600">
                        <i class="bi bi-arrow-left"></i> Back to Events
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div style="background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.3)">
                <!-- Header -->
                <div style="background:<?= $reg['checked_in_at'] ? '#fffbeb' : '#ecfdf5' ?>;padding:28px;text-align:center;border-bottom:1px solid <?= $reg['checked_in_at'] ? '#fde68a' : '#a7f3d0' ?>">
                    <div style="width:72px;height:72px;border-radius:50%;background:<?= $reg['checked_in_at'] ? 'rgba(245,158,11,.15)' : 'rgba(16,185,129,.15)' ?>;border:2px solid <?= $reg['checked_in_at'] ? 'rgba(245,158,11,.3)' : 'rgba(16,185,129,.3)' ?>;display:flex;align-items:center;justify-content:center;font-size:30px;color:<?= $reg['checked_in_at'] ? '#f59e0b' : '#10b981' ?>;margin:0 auto 14px">
                        <i class="bi bi-<?= $reg['checked_in_at'] ? 'exclamation-circle-fill' : 'check-circle-fill' ?>"></i>
                    </div>
                    <h2 style="font-size:20px;font-weight:800;color:<?= $reg['checked_in_at'] ? '#92400e' : '#065f46' ?>;margin-bottom:6px">
                        <?= $reg['checked_in_at'] ? 'Already Checked In' : 'Registration Verified' ?>
                    </h2>
                    <p style="font-size:13px;color:<?= $reg['checked_in_at'] ? '#a16207' : '#047857' ?>;margin:0">
                        <?= $reg['checked_in_at'] ? 'This attendee has already been checked in.' : 'This registration is valid and confirmed.' ?>
                    </p>
                </div>

                <!-- Attendee Details -->
                <div style="padding:24px">
                    <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid #f0f4f8">
                        <div style="width:52px;height:52px;border-radius:50%;background:linear-gradient(135deg,#1e3a5f,#274d80);display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:800;color:#fff;flex-shrink:0">
                            <?= $e(Helpers\Helper::avatarInitials($reg['first_name'].' '.$reg['last_name'])) ?>
                        </div>
                        <div>
                            <div style="font-size:18px;font-weight:800;color:#0f172a"><?= $e($reg['first_name'].' '.$reg['last_name']) ?></div>
                            <code style="font-size:12px;background:#e8f0f9;color:#1e3a5f;padding:2px 8px;border-radius:5px;font-weight:700"><?= $e($reg['registration_code']) ?></code>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                        <?php
                        $rows = [
                            ['bi-calendar3','Event',$reg['event_title']],
                            ['bi-building','Church',$reg['church_name']?: '—'],
                            ['bi-telephone-fill','Phone',$reg['phone']],
                            ['bi-envelope-fill','Email',$reg['email']],
                        ];
                        if ($reg['checked_in_at']): $rows[] = ['bi-clock-history','Checked In',Helpers\Helper::formatDateTime($reg['checked_in_at'])]; endif;
                        foreach($rows as [$icon,$label,$value]):
                        ?>
                        <div style="background:#f8fafc;border-radius:10px;padding:12px">
                            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#94a3b8;margin-bottom:4px">
                                <i class="bi <?= $icon ?> me-1"></i><?= $label ?>
                            </div>
                            <div style="font-size:13px;font-weight:600;color:#0f172a;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= $e($value) ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <p style="text-align:center;font-size:12px;color:#94a3b8;margin-top:20px;margin-bottom:0">
                        <i class="bi bi-shield-check me-1" style="color:#10b981"></i>
                        Please present this to an event official for physical check-in.
                    </p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
