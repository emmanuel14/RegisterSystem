<?php
$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$pageTitle = 'Reports';
?>

<div class="page-header">
    <div>
        <h2 class="page-title">Reports</h2>
        <p class="page-subtitle">Registration & attendance analytics</p>
    </div>
    <div class="page-actions">
        <a href="<?= Helpers\Helper::base('admin/registrations/export-csv?' . ($eventId ? 'event_id=' . $eventId : '')) ?>" class="btn btn-outline-success btn-sm">
            <i class="bi bi-download me-1"></i> Export CSV
        </a>
    </div>
</div>

<!-- Event Filter -->
<div class="card ems-card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-sm-5 col-md-4">
                <label class="form-label fw-semibold small mb-1">Filter by Event</label>
                <select name="event_id" class="form-select form-select-sm">
                    <option value="">All Events</option>
                    <?php foreach ($events as $ev): ?>
                        <option value="<?= $ev['id'] ?>" <?= $eventId == $ev['id'] ? 'selected' : '' ?>>
                            <?= $e(Helpers\Helper::truncate($ev['title'], 50)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-sm btn-primary">Apply</button>
                <a href="<?= Helpers\Helper::base('admin/reports') ?>" class="btn btn-sm btn-outline-secondary ms-1">All</a>
            </div>
        </form>
    </div>
</div>

<!-- Summary Stats -->
<div class="row g-4 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card stat-card--blue">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <div class="stat-body">
                <div class="stat-value"><?= number_format($totalRegs) ?></div>
                <div class="stat-label">Total Registrations</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card stat-card--green">
            <div class="stat-icon"><i class="bi bi-patch-check-fill"></i></div>
            <div class="stat-body">
                <div class="stat-value"><?= number_format($checkedIn) ?></div>
                <div class="stat-label">Checked In</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card stat-card--red">
            <div class="stat-icon"><i class="bi bi-person-x-fill"></i></div>
            <div class="stat-body">
                <div class="stat-value"><?= number_format($noShows) ?></div>
                <div class="stat-label">No-Shows</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card stat-card--gold">
            <div class="stat-icon"><i class="bi bi-graph-up"></i></div>
            <div class="stat-body">
                <div class="stat-value"><?= $attendancePct ?>%</div>
                <div class="stat-label">Attendance Rate</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Daily Registrations Chart -->
    <div class="col-lg-8">
        <div class="card ems-card">
            <div class="card-header"><h5 class="card-title mb-0">Daily Registrations (Last 30 Days)</h5></div>
            <div class="card-body">
                <canvas id="dailyChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Gender Breakdown -->
    <div class="col-lg-4">
        <div class="card ems-card">
            <div class="card-header"><h5 class="card-title mb-0">Gender Breakdown</h5></div>
            <div class="card-body">
                <canvas id="genderChart" height="200"></canvas>
                <div class="mt-3">
                    <?php foreach ($genders as $g): ?>
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-capitalize"><?= $e(str_replace('_',' ',$g['gender'])) ?></span>
                        <strong><?= number_format($g['total']) ?></strong>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Churches -->
    <div class="col-lg-6">
        <div class="card ems-card">
            <div class="card-header"><h5 class="card-title mb-0">Top Churches / Organizations</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table ems-table mb-0">
                        <thead><tr><th>#</th><th>Church</th><th class="text-end">Registrations</th></tr></thead>
                        <tbody>
                            <?php foreach ($churches as $i => $c): ?>
                            <tr>
                                <td class="text-muted small"><?= $i+1 ?></td>
                                <td><?= $e($c['church_name'] ?: 'Not Specified') ?></td>
                                <td class="text-end"><span class="badge bg-primary"><?= number_format($c['total']) ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($churches)): ?>
                                <tr><td colspan="3" class="text-muted text-center py-3">No data</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Top States -->
    <div class="col-lg-6">
        <div class="card ems-card">
            <div class="card-header"><h5 class="card-title mb-0">Top States</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table ems-table mb-0">
                        <thead><tr><th>#</th><th>State</th><th class="text-end">Registrations</th></tr></thead>
                        <tbody>
                            <?php foreach ($states as $i => $s): ?>
                            <tr>
                                <td class="text-muted small"><?= $i+1 ?></td>
                                <td><?= $e($s['state'] ?: 'Not Specified') ?></td>
                                <td class="text-end"><span class="badge bg-secondary"><?= number_format($s['total']) ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($states)): ?>
                                <tr><td colspan="3" class="text-muted text-center py-3">No data</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// ── Daily Chart ───────────────────────────────────────────────────────────
const dailyData  = <?= json_encode($daily) ?>;
const dailyCtx   = document.getElementById('dailyChart').getContext('2d');
new Chart(dailyCtx, {
    type: 'bar',
    data: {
        labels: dailyData.map(d => d.day),
        datasets: [{
            label: 'Registrations',
            data: dailyData.map(d => d.total),
            backgroundColor: 'rgba(26,60,94,0.75)',
            borderColor: '#1a3c5e',
            borderWidth: 1,
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});

// ── Gender Doughnut ───────────────────────────────────────────────────────
const genderData = <?= json_encode($genders) ?>;
const genderCtx  = document.getElementById('genderChart').getContext('2d');
new Chart(genderCtx, {
    type: 'doughnut',
    data: {
        labels: genderData.map(g => g.gender.replace(/_/g,' ')),
        datasets: [{
            data: genderData.map(g => g.total),
            backgroundColor: ['#1a3c5e','#c8a456','#6c757d','#17a2b8'],
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } }
    }
});
</script>
