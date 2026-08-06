<?php
$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$pageTitle = $event['title'];
$attendancePct = $stats['total'] > 0 ? round(($stats['checked_in'] / $stats['total']) * 100, 1) : 0;
?>

<div class="page-header">
    <div>
        <h2 class="page-title"><?= $e($event['title']) ?></h2>
        <?php if ($event['theme']): ?>
            <p class="page-subtitle"><?= $e($event['theme']) ?></p>
        <?php endif; ?>
    </div>
    <div class="page-actions d-flex gap-2">
        <a href="<?= Helpers\Helper::base('admin/events/' . $event['id'] . '/edit') ?>" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <a href="<?= Helpers\Helper::base('admin/events') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Banner -->
        <?php if ($event['banner_image']): ?>
            <img src="/uploads/banners/<?= $e($event['banner_image']) ?>"
                 class="img-fluid rounded mb-4 w-100" style="max-height:320px;object-fit:cover" alt="Banner">
        <?php endif; ?>

        <!-- Description -->
        <div class="card ems-card mb-4">
            <div class="card-header"><h5 class="card-title mb-0">Description</h5></div>
            <div class="card-body">
                <?= nl2br($e($event['description'] ?: 'No description provided.')) ?>
            </div>
        </div>

        <!-- Speakers -->
        <?php if ($speakers): ?>
        <div class="card ems-card mb-4">
            <div class="card-header"><h5 class="card-title mb-0">Speakers</h5></div>
            <div class="card-body">
                <div class="row g-3">
                    <?php foreach ($speakers as $sp): ?>
                    <div class="col-sm-6">
                        <div class="d-flex gap-3 align-items-start">
                            <div class="avatar-circle"><?= $e(Helpers\Helper::avatarInitials($sp['name'])) ?></div>
                            <div>
                                <div class="fw-semibold"><?= $e($sp['name']) ?></div>
                                <?php if ($sp['title']): ?>
                                    <div class="small text-muted"><?= $e($sp['title']) ?></div>
                                <?php endif; ?>
                                <?php if ($sp['bio']): ?>
                                    <div class="small mt-1"><?= $e($sp['bio']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Schedule -->
        <?php if ($schedule): ?>
        <div class="card ems-card mb-4">
            <div class="card-header"><h5 class="card-title mb-0">Schedule</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table ems-table mb-0">
                        <thead>
                            <tr><th>Day</th><th>Time</th><th>Session</th><th>Speaker</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schedule as $item): ?>
                            <tr>
                                <td class="small"><?= Helpers\Helper::formatDate($item['day']) ?></td>
                                <td class="small text-muted"><?= $e(substr($item['start_time'],0,5)) ?><?= $item['end_time'] ? ' – ' . substr($item['end_time'],0,5) : '' ?></td>
                                <td>
                                    <div class="fw-semibold small"><?= $e($item['title']) ?></div>
                                    <?php if ($item['description']): ?>
                                        <small class="text-muted"><?= $e($item['description']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="small"><?= $e($item['speaker_name'] ?? '—') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-4">
        <!-- Stats -->
        <div class="card ems-card mb-4">
            <div class="card-header"><h5 class="card-title mb-0">Registration Stats</h5></div>
            <div class="card-body">
                <div class="row g-3 text-center">
                    <div class="col-6">
                        <div class="fs-2 fw-bold text-primary"><?= number_format($stats['total']) ?></div>
                        <div class="small text-muted">Registered</div>
                    </div>
                    <div class="col-6">
                        <div class="fs-2 fw-bold text-success"><?= number_format($stats['checked_in']) ?></div>
                        <div class="small text-muted">Checked In</div>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Attendance</span>
                        <strong><?= $attendancePct ?>%</strong>
                    </div>
                    <div class="progress" style="height:8px">
                        <div class="progress-bar bg-success" style="width:<?= $attendancePct ?>%"></div>
                    </div>
                </div>
                <?php if ($event['capacity']): ?>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Capacity</span>
                            <strong><?= number_format($stats['total']) ?> / <?= number_format($event['capacity']) ?></strong>
                        </div>
                        <?php $capPct = round(($stats['total'] / $event['capacity']) * 100, 1); ?>
                        <div class="progress" style="height:8px">
                            <div class="progress-bar bg-warning" style="width:<?= min(100,$capPct) ?>%"></div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Event Info -->
        <div class="card ems-card mb-4">
            <div class="card-header"><h5 class="card-title mb-0">Event Info</h5></div>
            <div class="card-body">
                <dl class="info-list">
                    <dt>Status</dt>
                    <dd><?php
                        $sc = ['published'=>'success','draft'=>'secondary','cancelled'=>'danger','completed'=>'info'][$event['status']] ?? 'secondary';
                        ?><span class="badge bg-<?= $sc ?>"><?= ucfirst($e($event['status'])) ?></span></dd>
                    <dt>Registration</dt>
                    <dd><span class="badge bg-<?= $event['registration_status']==='open'?'success':'danger' ?>"><?= ucfirst($e($event['registration_status'])) ?></span></dd>
                    <dt>Start</dt>
                    <dd><?= Helpers\Helper::formatDateTime($event['start_date']) ?></dd>
                    <dt>End</dt>
                    <dd><?= Helpers\Helper::formatDateTime($event['end_date']) ?></dd>
                    <?php if ($event['venue']): ?>
                    <dt>Venue</dt>
                    <dd><?= $e($event['venue']) ?></dd>
                    <?php endif; ?>
                    <?php if ($event['city']): ?>
                    <dt>Location</dt>
                    <dd><?= $e($event['city']) ?><?= $event['state'] ? ', ' . $e($event['state']) : '' ?></dd>
                    <?php endif; ?>
                    <dt>Capacity</dt>
                    <dd><?= $event['capacity'] ? number_format($event['capacity']) : 'Unlimited' ?></dd>
                </dl>
            </div>
        </div>

        <!-- Actions -->
        <div class="d-grid gap-2">
            <a href="<?= Helpers\Helper::base('admin/registrations?event_id=' . $event['id']) ?>" class="btn btn-outline-primary">
                <i class="bi bi-people me-1"></i> View Registrations
            </a>
            <a href="<?= Helpers\Helper::base('events/' . $e($event['slug'])) ?>" target="_blank" class="btn btn-outline-secondary">
                <i class="bi bi-globe me-1"></i> View Public Page
            </a>
        </div>
    </div>
</div>
