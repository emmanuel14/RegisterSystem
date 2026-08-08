<?php
$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$pageTitle = 'Registration Details';
$B = fn(string $p = '') => Helpers\Helper::base($p);
?>

<div class="page-header">
    <div>
        <div class="ems-breadcrumb">
            <a href="<?= $B('admin/registrations') ?>">Registrations</a>
            <span class="sep"><i class="bi bi-chevron-right" style="font-size:10px"></i></span>
            <span><?= $e($reg['registration_code']) ?></span>
        </div>
        <h1 class="page-title"><?= $e($reg['first_name'].' '.$reg['last_name']) ?></h1>
        <p class="page-subtitle">Registration details and check-in status</p>
    </div>
    <div class="page-actions">
        <a href="<?= $B('admin/registrations/'.$reg['id'].'/print') ?>" target="_blank" class="btn btn-outline-secondary">
            <i class="bi bi-printer"></i> Print Pass
        </a>
        <a href="<?= $B('admin/registrations/'.$reg['id'].'/edit') ?>" class="btn btn-primary">
            <i class="bi bi-pencil"></i> Edit
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Left column -->
    <div class="col-lg-8">

        <!-- Attendee info card -->
        <div class="ems-card mb-4">
            <div class="card-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar avatar-lg"><?= $e(Helpers\Helper::avatarInitials($reg['first_name'].' '.$reg['last_name'])) ?></div>
                    <div>
                        <div class="card-title"><?= $e($reg['first_name'].' '.$reg['last_name']) ?></div>
                        <div class="card-subtitle"><?= $e($reg['email']) ?></div>
                    </div>
                </div>
                <?php
                $sc = ['confirmed'=>'green','pending'=>'amber','cancelled'=>'red','waitlisted'=>'gray'][$reg['status']] ?? 'gray';
                ?>
                <span class="ems-badge ems-badge--<?= $sc ?>"><?= ucfirst($e($reg['status'])) ?></span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <?php
                    $fields = [
                        ['bi-person-fill','First Name',$reg['first_name']],
                        ['bi-person-fill','Last Name',$reg['last_name']],
                        ['bi-envelope-fill','Email',$reg['email']],
                        ['bi-telephone-fill','Phone',$reg['phone']],
                        ['bi-gender-ambiguous','Gender',ucfirst(str_replace('_',' ',$reg['gender']))],
                        ['bi-calendar3','Date of Birth',$reg['date_of_birth'] ? Helpers\Helper::formatDate($reg['date_of_birth']) : '—'],
                        ['bi-building','Church / Org',$reg['church_name'] ?: '—'],
                        ['bi-map','State',$reg['state'] ?: '—'],
                        ['bi-geo-alt-fill','City',$reg['city'] ?: '—'],
                        ['bi-house-fill','Address',$reg['address'] ?: '—'],
                    ];
                    if ($reg['emergency_contact_name']) {
                        $fields[] = ['bi-person-plus-fill','Emergency Contact',$reg['emergency_contact_name'].' — '.$reg['emergency_contact_phone']];
                    }
                    foreach ($fields as [$icon,$label,$value]):
                    ?>
                    <div class="col-sm-6">
                        <div style="display:flex;align-items:flex-start;gap:10px">
                            <div style="width:32px;height:32px;border-radius:8px;background:var(--ems-primary-light);display:flex;align-items:center;justify-content:center;color:var(--ems-primary);font-size:13px;flex-shrink:0">
                                <i class="bi <?= $icon ?>"></i>
                            </div>
                            <div>
                                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--text-muted)"><?= $label ?></div>
                                <div style="font-size:13.5px;font-weight:500;color:var(--text-primary);margin-top:1px"><?= $e($value) ?></div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Event info -->
        <div class="ems-card">
            <div class="card-header">
                <div class="card-title"><i class="bi bi-calendar3 me-2" style="color:var(--ems-gold)"></i>Event Information</div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <?php
                    $evFields = [
                        ['bi-calendar-event','Event',$reg['event_title']],
                        ['bi-geo-alt-fill','Venue',$reg['venue'] ?: '—'],
                        ['bi-calendar3','Start Date',Helpers\Helper::formatDateTime($reg['start_date'])],
                        ['bi-clock-history','Registered At',Helpers\Helper::formatDateTime($reg['registered_at'])],
                    ];
                    foreach ($evFields as [$icon,$label,$value]):
                    ?>
                    <div class="col-sm-6">
                        <div style="display:flex;align-items:flex-start;gap:10px">
                            <div style="width:32px;height:32px;border-radius:8px;background:var(--ems-gold-light);display:flex;align-items:center;justify-content:center;color:var(--ems-gold);font-size:13px;flex-shrink:0">
                                <i class="bi <?= $icon ?>"></i>
                            </div>
                            <div>
                                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--text-muted)"><?= $label ?></div>
                                <div style="font-size:13.5px;font-weight:500;color:var(--text-primary);margin-top:1px"><?= $e($value) ?></div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Right column -->
    <div class="col-lg-4">

        <!-- QR Code -->
        <div class="ems-card mb-4">
            <div class="card-header">
                <div class="card-title"><i class="bi bi-qr-code me-2" style="color:var(--ems-gold)"></i>QR Code</div>
            </div>
            <div class="card-body text-center">
                <?php if ($qrUrl): ?>
                    <div class="qr-preview mb-3" style="display:inline-block">
                        <img src="<?= $e($qrUrl) ?>" alt="QR Code" width="160">
                    </div>
                <?php else: ?>
                    <div style="width:160px;height:160px;border:2px dashed var(--border);border-radius:10px;display:flex;align-items:center;justify-content:center;color:var(--text-muted);font-size:12px;margin:0 auto 12px">
                        Not generated
                    </div>
                <?php endif; ?>
                <div style="font-family:monospace;font-size:12px;font-weight:700;color:var(--ems-primary);background:var(--ems-primary-light);padding:6px 14px;border-radius:6px;display:inline-block;margin-bottom:14px">
                    <?= $e($reg['registration_code']) ?>
                </div>
                <div class="d-grid">
                    <a href="<?= $B('registration/download-qr/'.$e($reg['registration_code'])) ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-download me-1"></i> Download QR PNG
                    </a>
                </div>
            </div>
        </div>

        <!-- Check-in status -->
        <div class="ems-card">
            <div class="card-header">
                <div class="card-title"><i class="bi bi-patch-check me-2" style="color:var(--ems-gold)"></i>Check-in</div>
            </div>
            <div class="card-body text-center" style="padding:28px 22px">
                <?php if ($reg['checked_in_at']): ?>
                    <div style="width:64px;height:64px;border-radius:50%;background:var(--green-bg);border:2px solid #a7f3d0;display:flex;align-items:center;justify-content:center;font-size:28px;color:var(--green);margin:0 auto 14px">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div style="font-size:15px;font-weight:800;color:var(--green);margin-bottom:5px">Checked In</div>
                    <div style="font-size:13px;color:var(--text-secondary)"><?= Helpers\Helper::formatDateTime($reg['checked_in_at']) ?></div>
                <?php else: ?>
                    <div style="width:64px;height:64px;border-radius:50%;background:#f8fafc;border:2px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:28px;color:var(--text-muted);margin:0 auto 14px">
                        <i class="bi bi-circle"></i>
                    </div>
                    <div style="font-size:14px;font-weight:600;color:var(--text-secondary);margin-bottom:16px">Not yet checked in</div>
                    <button class="btn btn-success w-100 btn-checkin" data-id="<?= $reg['id'] ?>" style="border-radius:8px;font-weight:700;padding:12px">
                        <i class="bi bi-patch-check me-2"></i>Check In Now
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelector('.btn-checkin')?.addEventListener('click', async function () {
    this.disabled = true;
    this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Checking in…';
    const res  = await fetch('<?= $B('admin/checkin/'.$reg['id']) ?>', {
        method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'<?= CSRF_TOKEN_NAME ?>=<?= $e($csrf) ?>'
    });
    const data = await res.json();
    if (data.success) {
        Swal.fire({icon:'success',title:'Checked In!',timer:1500,showConfirmButton:false});
        setTimeout(()=>location.reload(),1600);
    } else {
        Swal.fire({icon:'info',title:data.message});
        this.disabled = false;
        this.innerHTML = '<i class="bi bi-patch-check me-2"></i>Check In Now';
    }
});
</script>
