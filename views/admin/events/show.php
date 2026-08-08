<?php
$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$pageTitle = $event['title'];
$B = fn(string $p = '') => Helpers\Helper::base($p);
$attendancePct = $stats['total'] > 0 ? round(($stats['checked_in']/$stats['total'])*100,1) : 0;
?>

<div class="page-header">
    <div>
        <div class="ems-breadcrumb">
            <a href="<?= $B('admin/events') ?>">Events</a>
            <span class="sep"><i class="bi bi-chevron-right" style="font-size:10px"></i></span>
            <span><?= $e($event['title']) ?></span>
        </div>
        <h1 class="page-title"><?= $e($event['title']) ?></h1>
        <?php if ($event['theme']): ?><p class="page-subtitle">"<?= $e($event['theme']) ?>"</p><?php endif; ?>
    </div>
    <div class="page-actions">
        <a href="<?= $B('events/'.$e($event['slug'])) ?>" target="_blank" class="btn btn-outline-secondary"><i class="bi bi-globe2"></i> View Public</a>
        <a href="<?= $B('admin/events/'.$event['id'].'/edit') ?>" class="btn btn-primary"><i class="bi bi-pencil"></i> Edit</a>
    </div>
</div>

<!-- Banner -->
<?php if ($event['banner_image']): ?>
<div style="border-radius:var(--r-lg);overflow:hidden;margin-bottom:24px;height:260px;box-shadow:var(--shadow-md)">
    <img src="<?= $B('uploads/banners/'.$e($event['banner_image'])) ?>" alt="Banner" style="width:100%;height:100%;object-fit:cover">
</div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Stats -->
        <div class="row g-3 mb-4">
            <div class="col-6">
                <div class="stat-card stat-card--blue" style="padding:18px">
                    <div class="stat-card-header"><div class="stat-icon" style="width:38px;height:38px"><i class="bi bi-people-fill"></i></div></div>
                    <div class="stat-value" style="font-size:24px"><?= number_format($stats['total']) ?></div>
                    <div class="stat-label">Registered</div>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-card stat-card--green" style="padding:18px">
                    <div class="stat-card-header"><div class="stat-icon" style="width:38px;height:38px"><i class="bi bi-patch-check-fill"></i></div></div>
                    <div class="stat-value" style="font-size:24px"><?= number_format($stats['checked_in']) ?></div>
                    <div class="stat-label">Checked In</div>
                </div>
            </div>
        </div>

        <!-- Attendance progress -->
        <div class="ems-card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span style="font-size:13px;font-weight:700;color:var(--text-primary)">Attendance Rate</span>
                    <span style="font-size:13px;font-weight:800;color:var(--ems-primary)"><?= $attendancePct ?>%</span>
                </div>
                <div class="ems-progress">
                    <div class="ems-progress-bar" style="width:<?= $attendancePct ?>%;background:linear-gradient(90deg,#1e3a5f,#3b82f6)"></div>
                </div>
                <?php if ($event['capacity']): ?>
                <?php $capPct = min(100,round(($stats['total']/$event['capacity'])*100,1)); ?>
                <div class="d-flex justify-content-between mt-3 mb-2">
                    <span style="font-size:13px;font-weight:700;color:var(--text-primary)">Capacity Used</span>
                    <span style="font-size:13px;font-weight:800;color:var(--ems-gold)"><?= $capPct ?>%</span>
                </div>
                <div class="ems-progress">
                    <div class="ems-progress-bar" style="width:<?= $capPct ?>%;background:linear-gradient(90deg,#c9963a,#e8b84b)"></div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Description -->
        <div class="ems-card mb-4">
            <div class="card-header"><div class="card-title">Description</div></div>
            <div class="card-body" style="line-height:1.8;color:#374151">
                <?= $event['description'] ? nl2br($e($event['description'])) : '<span style="color:var(--text-muted)">No description provided.</span>' ?>
            </div>
        </div>

        <!-- Speakers -->
        <?php if ($speakers): ?>
        <div class="ems-card mb-4">
            <div class="card-header"><div class="card-title"><i class="bi bi-mic-fill me-2" style="color:var(--ems-gold)"></i>Speakers</div></div>
            <div class="card-body">
                <div class="row g-3">
                    <?php foreach ($speakers as $sp): ?>
                    <div class="col-sm-6">
                        <div style="display:flex;gap:12px;align-items:flex-start;background:#f8fafc;border-radius:10px;padding:14px">
                            <div class="avatar" style="width:40px;height:40px;font-size:15px"><?= $e(Helpers\Helper::avatarInitials($sp['name'])) ?></div>
                            <div>
                                <div style="font-size:14px;font-weight:700"><?= $e($sp['name']) ?></div>
                                <?php if ($sp['title']): ?><div style="font-size:12px;color:var(--ems-gold);font-weight:600"><?= $e($sp['title']) ?></div><?php endif; ?>
                                <?php if ($sp['bio']): ?><div style="font-size:12.5px;color:var(--text-secondary);margin-top:4px"><?= $e($sp['bio']) ?></div><?php endif; ?>
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
        <div class="ems-card">
            <div class="card-header"><div class="card-title"><i class="bi bi-list-ol me-2" style="color:var(--ems-gold)"></i>Schedule</div></div>
            <div class="table-responsive">
                <table class="ems-table">
                    <thead><tr><th>Day</th><th>Time</th><th>Session</th><th>Speaker</th></tr></thead>
                    <tbody>
                        <?php foreach ($schedule as $item): ?>
                        <tr>
                            <td class="cell-muted"><?= Helpers\Helper::formatDate($item['day'],'M j') ?></td>
                            <td style="font-size:12px;font-weight:700;color:var(--text-muted);white-space:nowrap"><?= $e(substr($item['start_time'],0,5)) ?><?= $item['end_time']?' – '.substr($item['end_time'],0,5):'' ?></td>
                            <td>
                                <div class="cell-primary"><?= $e($item['title']) ?></div>
                                <?php if ($item['description']): ?><div class="cell-muted"><?= $e($item['description']) ?></div><?php endif; ?>
                            </td>
                            <td class="cell-muted"><?= $e($item['speaker_name'] ?? '—') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-4">
        <!-- Event Info -->
        <div class="ems-card mb-4">
            <div class="card-header"><div class="card-title">Event Details</div></div>
            <div class="card-body" style="padding:16px 22px">
                <?php
                $sc = ['published'=>'green','draft'=>'gray','cancelled'=>'red','completed'=>'blue'][$event['status']] ?? 'gray';
                $rc = $event['registration_status']==='open'?'green':'red';
                $rows = [
                    ['Status', '<span class="ems-badge ems-badge--'.$sc.'">'.ucfirst($e($event['status'])).'</span>'],
                    ['Registration', '<span class="ems-badge ems-badge--'.$rc.'">'.ucfirst($e($event['registration_status'])).'</span>'],
                    ['Start', Helpers\Helper::formatDateTime($event['start_date'])],
                    ['End', Helpers\Helper::formatDateTime($event['end_date'])],
                    ['Venue', $event['venue'] ? $e($event['venue']) : '—'],
                    ['Location', $event['city'] ? $e($event['city']).($event['state']?', '.$e($event['state']):'') : '—'],
                    ['Capacity', $event['capacity'] ? number_format($event['capacity']).' seats' : 'Unlimited'],
                ];
                foreach ($rows as [$lbl,$val]):
                ?>
                <div class="info-list-row">
                    <div class="info-label"><?= $lbl ?></div>
                    <div class="info-value"><?= $val ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="d-grid gap-2">
            <a href="<?= $B('admin/registrations?event_id='.$event['id']) ?>" class="btn btn-primary">
                <i class="bi bi-people-fill me-1"></i> View Registrations
            </a>
            <a href="<?= $B('admin/checkin') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-qr-code-scan me-1"></i> Go to Check-In
            </a>
            <a href="<?= $B('admin/reports?event_id='.$event['id']) ?>" class="btn btn-outline-secondary">
                <i class="bi bi-bar-chart me-1"></i> View Reports
            </a>
        </div>
    </div>
</div>
