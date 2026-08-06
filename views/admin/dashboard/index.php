<?php
$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$pageTitle = 'Dashboard';
?>

<div class="page-header">
    <div>
        <h2 class="page-title">Dashboard</h2>
        <p class="page-subtitle">Welcome back, <?= $e($admin['name']) ?></p>
    </div>
    <div class="page-actions">
        <a href="<?= Helpers\Helper::base('admin/events/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> New Event
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card stat-card--blue">
            <div class="stat-icon"><i class="bi bi-calendar3"></i></div>
            <div class="stat-body">
                <div class="stat-value"><?= number_format($stats['total']) ?></div>
                <div class="stat-label">Total Events</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card stat-card--green">
            <div class="stat-icon"><i class="bi bi-calendar-check"></i></div>
            <div class="stat-body">
                <div class="stat-value"><?= number_format($stats['active']) ?></div>
                <div class="stat-label">Active Events</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card stat-card--gold">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <div class="stat-body">
                <div class="stat-value"><?= number_format($stats['total_regs']) ?></div>
                <div class="stat-label">Registrations</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card stat-card--purple">
            <div class="stat-icon"><i class="bi bi-patch-check-fill"></i></div>
            <div class="stat-body">
                <div class="stat-value"><?= number_format($stats['checked_in']) ?></div>
                <div class="stat-label">Checked In</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Registrations -->
    <div class="col-lg-8">
        <div class="card ems-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="bi bi-clock-history me-2"></i>Recent Registrations</h5>
                <a href="<?= Helpers\Helper::base('admin/registrations') ?>" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($stats['recent_regs'])): ?>
                    <div class="empty-state py-5">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                        <p class="text-muted mt-2">No registrations yet.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover ems-table mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Event</th>
                                    <th>Code</th>
                                    <th>Registered</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stats['recent_regs'] as $reg): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar-sm"><?= $e(Helpers\Helper::avatarInitials($reg['attendee_name'])) ?></div>
                                                <div>
                                                    <div class="fw-semibold"><?= $e($reg['attendee_name']) ?></div>
                                                    <small class="text-muted"><?= $e($reg['email']) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="text-truncate d-inline-block" style="max-width:160px"><?= $e($reg['event_title']) ?></span></td>
                                        <td><code class="reg-code"><?= $e($reg['registration_code']) ?></code></td>
                                        <td class="text-muted small"><?= Helpers\Helper::formatDateTime($reg['registered_at'], 'M j, g:i A') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4">
        <div class="card ems-card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-lightning-fill me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body d-flex flex-column gap-3 pt-3">
                <a href="<?= Helpers\Helper::base('admin/events/create') ?>" class="quick-action-btn">
                    <i class="bi bi-calendar-plus"></i>
                    <span>Create Event</span>
                    <i class="bi bi-chevron-right ms-auto"></i>
                </a>
                <a href="<?= Helpers\Helper::base('admin/checkin') ?>" class="quick-action-btn">
                    <i class="bi bi-qr-code-scan"></i>
                    <span>QR Check-In</span>
                    <i class="bi bi-chevron-right ms-auto"></i>
                </a>
                <a href="<?= Helpers\Helper::base('admin/registrations/export-csv') ?>" class="quick-action-btn">
                    <i class="bi bi-download"></i>
                    <span>Export CSV</span>
                    <i class="bi bi-chevron-right ms-auto"></i>
                </a>
                <a href="<?= Helpers\Helper::base('admin/reports') ?>" class="quick-action-btn">
                    <i class="bi bi-bar-chart"></i>
                    <span>View Reports</span>
                    <i class="bi bi-chevron-right ms-auto"></i>
                </a>
                <a href="<?= Helpers\Helper::base() ?>" target="_blank" class="quick-action-btn">
                    <i class="bi bi-globe"></i>
                    <span>Public Site</span>
                    <i class="bi bi-chevron-right ms-auto"></i>
                </a>
            </div>
        </div>
    </div>
</div>
