<?php
$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$pageTitle = 'Registration: ' . $reg['registration_code'];
?>

<div class="page-header">
    <div>
        <h2 class="page-title">Registration Details</h2>
        <p class="page-subtitle"><code><?= $e($reg['registration_code']) ?></code></p>
    </div>
    <div class="page-actions d-flex gap-2">
        <a href="<?= Helpers\Helper::base('admin/registrations/' . $reg['id'] . '/edit') ?>" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <a href="<?= Helpers\Helper::base('admin/registrations/' . $reg['id'] . '/print') ?>" target="_blank" class="btn btn-outline-secondary">
            <i class="bi bi-printer me-1"></i> Print Pass
        </a>
        <a href="<?= Helpers\Helper::base('admin/registrations') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <!-- Attendee Info -->
        <div class="card ems-card mb-4">
            <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-person me-2"></i>Attendee Information</h5></div>
            <div class="card-body">
                <dl class="info-list info-list--2col">
                    <dt>First Name</dt>
                    <dd><?= $e($reg['first_name']) ?></dd>
                    <dt>Last Name</dt>
                    <dd><?= $e($reg['last_name']) ?></dd>
                    <dt>Email</dt>
                    <dd><a href="mailto:<?= $e($reg['email']) ?>"><?= $e($reg['email']) ?></a></dd>
                    <dt>Phone</dt>
                    <dd><?= $e($reg['phone']) ?></dd>
                    <dt>Gender</dt>
                    <dd class="text-capitalize"><?= $e(str_replace('_', ' ', $reg['gender'])) ?></dd>
                    <?php if ($reg['date_of_birth']): ?>
                    <dt>Date of Birth</dt>
                    <dd><?= Helpers\Helper::formatDate($reg['date_of_birth']) ?></dd>
                    <?php endif; ?>
                    <dt>Church</dt>
                    <dd><?= $e($reg['church_name'] ?: '—') ?></dd>
                    <dt>State</dt>
                    <dd><?= $e($reg['state'] ?: '—') ?></dd>
                    <dt>City</dt>
                    <dd><?= $e($reg['city'] ?: '—') ?></dd>
                    <?php if ($reg['address']): ?>
                    <dt>Address</dt>
                    <dd><?= $e($reg['address']) ?></dd>
                    <?php endif; ?>
                    <?php if ($reg['emergency_contact_name']): ?>
                    <dt>Emergency Contact</dt>
                    <dd><?= $e($reg['emergency_contact_name']) ?> — <?= $e($reg['emergency_contact_phone']) ?></dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>

        <!-- Event Info -->
        <div class="card ems-card">
            <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-calendar me-2"></i>Event</h5></div>
            <div class="card-body">
                <dl class="info-list info-list--2col">
                    <dt>Event</dt>
                    <dd><a href="<?= Helpers\Helper::base('admin/events/' . urlencode($reg['event_slug'])) ?>"><?= $e($reg['event_title']) ?></a></dd>
                    <dt>Venue</dt>
                    <dd><?= $e($reg['venue'] ?: '—') ?></dd>
                    <dt>Start</dt>
                    <dd><?= Helpers\Helper::formatDateTime($reg['start_date']) ?></dd>
                    <dt>Registered</dt>
                    <dd><?= Helpers\Helper::formatDateTime($reg['registered_at']) ?></dd>
                    <dt>Status</dt>
                    <dd><?php
                        $sc = ['confirmed'=>'success','pending'=>'warning','cancelled'=>'danger','waitlisted'=>'secondary'][$reg['status']] ?? 'secondary';
                        ?><span class="badge bg-<?= $sc ?>"><?= ucfirst($e($reg['status'])) ?></span></dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <!-- QR Code -->
        <div class="card ems-card mb-4 text-center">
            <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-qr-code me-2"></i>QR Code</h5></div>
            <div class="card-body">
                <?php if ($qrUrl): ?>
                    <img src="<?= $e($qrUrl) ?>" alt="QR Code" class="img-fluid mb-3" style="max-width:200px">
                <?php else: ?>
                    <div class="alert alert-warning small">QR code not yet generated.</div>
                <?php endif; ?>
                <code class="d-block mb-3 fs-6"><?= $e($reg['registration_code']) ?></code>
                <a href="<?= Helpers\Helper::base('registration/download-qr/' . $e($reg['registration_code'])) ?>" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-download me-1"></i> Download QR
                </a>
            </div>
        </div>

        <!-- Check-in Status -->
        <div class="card ems-card">
            <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-patch-check me-2"></i>Check-in</h5></div>
            <div class="card-body text-center">
                <?php if ($reg['checked_in_at']): ?>
                    <div class="text-success mb-2">
                        <i class="bi bi-check-circle-fill fs-1"></i>
                    </div>
                    <div class="fw-bold text-success mb-1">Checked In</div>
                    <div class="small text-muted"><?= Helpers\Helper::formatDateTime($reg['checked_in_at']) ?></div>
                <?php else: ?>
                    <div class="text-secondary mb-2">
                        <i class="bi bi-circle fs-1"></i>
                    </div>
                    <div class="text-muted mb-3">Not yet checked in</div>
                    <button class="btn btn-success btn-checkin" data-id="<?= $reg['id'] ?>">
                        <i class="bi bi-patch-check me-1"></i> Check In Now
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelector('.btn-checkin')?.addEventListener('click', async function () {
    const res  = await fetch(`/admin/checkin/<?= $reg['id'] ?>`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `<?= CSRF_TOKEN_NAME ?>=<?= $e($csrf) ?>`
    });
    const data = await res.json();
    if (data.success) {
        Swal.fire({ icon: 'success', title: 'Checked In!', timer: 1500, showConfirmButton: false });
        setTimeout(() => location.reload(), 1600);
    } else {
        Swal.fire({ icon: 'info', title: data.message });
    }
});
</script>
