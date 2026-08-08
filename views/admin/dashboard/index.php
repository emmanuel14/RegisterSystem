<?php
$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$pageTitle = 'Dashboard';
$B = fn(string $p = '') => Helpers\Helper::base($p);
?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <p class="page-subtitle mb-1" style="font-size:12px;text-transform:uppercase;letter-spacing:.6px;color:var(--text-muted);font-weight:700">
            <?= date('l, F j, Y') ?>
        </p>
        <h1 class="page-title">Good <?= date('H') < 12 ? 'morning' : (date('H') < 17 ? 'afternoon' : 'evening') ?>, <?= $e(explode(' ', $admin['name'])[0]) ?> 👋</h1>
        <p class="page-subtitle">Here's what's happening with your events today.</p>
    </div>
    <div class="page-actions">
        <a href="<?= $B('admin/events/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> New Event
        </a>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-4 mb-5 stagger-children">
    <div class="col-6 col-xl-3">
        <div class="stat-card stat-card--blue">
            <div class="stat-card-header">
                <div class="stat-icon"><i class="bi bi-calendar3"></i></div>
                <span class="stat-trend up"><i class="bi bi-arrow-up-right"></i> Active</span>
            </div>
            <div class="stat-value"><?= number_format($stats['total']) ?></div>
            <div class="stat-label">Total Events</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card stat-card--green">
            <div class="stat-card-header">
                <div class="stat-icon"><i class="bi bi-calendar-check-fill"></i></div>
                <span class="stat-trend up"><i class="bi bi-dot"></i> Live</span>
            </div>
            <div class="stat-value"><?= number_format($stats['active']) ?></div>
            <div class="stat-label">Published Events</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card stat-card--gold">
            <div class="stat-card-header">
                <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            </div>
            <div class="stat-value"><?= number_format($stats['total_regs']) ?></div>
            <div class="stat-label">Registrations</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card stat-card--purple">
            <div class="stat-card-header">
                <div class="stat-icon"><i class="bi bi-patch-check-fill"></i></div>
            </div>
            <div class="stat-value"><?= number_format($stats['checked_in']) ?></div>
            <div class="stat-label">Checked In</div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="row g-4">

    <!-- Recent Registrations -->
    <div class="col-lg-8">
        <div class="ems-card">
            <div class="card-header">
                <div>
                    <div class="card-title">Recent Registrations</div>
                    <div class="card-subtitle">Latest attendees across all events</div>
                </div>
                <a href="<?= $B('admin/registrations') ?>" class="btn btn-outline-secondary btn-sm">View all</a>
            </div>

            <?php if (empty($stats['recent_regs'])): ?>
                <div class="card-body">
                    <div class="empty-state">
                        <div class="empty-icon"><i class="bi bi-inbox"></i></div>
                        <div class="empty-title">No registrations yet</div>
                        <div class="empty-desc">Create and publish an event to start accepting registrations.</div>
                        <a href="<?= $B('admin/events/create') ?>" class="btn btn-primary btn-sm">Create Event</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="ems-table">
                        <thead>
                            <tr>
                                <th>Attendee</th>
                                <th>Event</th>
                                <th>Code</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stats['recent_regs'] as $reg): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar avatar-sm"><?= $e(Helpers\Helper::avatarInitials($reg['attendee_name'])) ?></div>
                                        <div>
                                            <div class="cell-primary"><?= $e($reg['attendee_name']) ?></div>
                                            <div class="cell-muted"><?= $e($reg['email']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="cell-muted" style="max-width:160px;display:inline-block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= $e($reg['event_title']) ?></span></td>
                                <td><span class="reg-code"><?= $e($reg['registration_code']) ?></span></td>
                                <td><span class="cell-muted"><?= Helpers\Helper::formatDateTime($reg['registered_at'], 'M j, g:i A') ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4">
        <div class="ems-card h-100">
            <div class="card-header">
                <div class="card-title">Quick Actions</div>
            </div>
            <div class="card-body d-flex flex-column gap-2 pt-3">
                <a href="<?= $B('admin/events/create') ?>" class="quick-action-btn">
                    <div class="qa-icon qa-icon--blue"><i class="bi bi-calendar-plus"></i></div>
                    <span>Create Event</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
                <a href="<?= $B('admin/checkin') ?>" class="quick-action-btn">
                    <div class="qa-icon qa-icon--green"><i class="bi bi-qr-code-scan"></i></div>
                    <span>QR Check-In</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
                <a href="<?= $B('admin/registrations/export-csv') ?>" class="quick-action-btn">
                    <div class="qa-icon qa-icon--gold"><i class="bi bi-file-earmark-spreadsheet"></i></div>
                    <span>Export CSV</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
                <a href="<?= $B('admin/reports') ?>" class="quick-action-btn">
                    <div class="qa-icon qa-icon--purple"><i class="bi bi-bar-chart-line"></i></div>
                    <span>View Reports</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
                <a href="<?= $B() ?>" target="_blank" class="quick-action-btn">
                    <div class="qa-icon" style="background:#f0fdf4;color:#16a34a"><i class="bi bi-globe2"></i></div>
                    <span>Public Website</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>
