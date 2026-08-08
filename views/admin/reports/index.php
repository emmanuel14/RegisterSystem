<?php
$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$pageTitle = 'Reports';
$B = fn(string $p = '') => Helpers\Helper::base($p);
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Reports &amp; Analytics</h1>
        <p class="page-subtitle">Registration and attendance insights</p>
    </div>
    <div class="page-actions">
        <a href="<?= $B('admin/registrations/export-csv'.($eventId?'?event_id='.$eventId:'')) ?>" class="btn btn-outline-secondary">
            <i class="bi bi-download"></i> Export CSV
        </a>
    </div>
</div>

<!-- Filter -->
<div class="filter-bar mb-5">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-sm-5 col-md-4">
            <label class="form-label" style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted)">Filter by Event</label>
            <select name="event_id" class="form-select">
                <option value="">All Events Combined</option>
                <?php foreach ($events as $ev): ?>
                    <option value="<?= $ev['id'] ?>" <?= $eventId==$ev['id']?'selected':'' ?>><?= $e(Helpers\Helper::truncate($ev['title'],50)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary">Apply Filter</button>
            <a href="<?= $B('admin/reports') ?>" class="btn btn-outline-secondary ms-1">Clear</a>
        </div>
    </form>
</div>

<!-- KPI Cards -->
<div class="row g-4 mb-5 stagger-children">
    <div class="col-6 col-xl-3">
        <div class="stat-card stat-card--blue">
            <div class="stat-card-header">
                <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            </div>
            <div class="stat-value"><?= number_format($totalRegs) ?></div>
            <div class="stat-label">Total Registrations</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card stat-card--green">
            <div class="stat-card-header">
                <div class="stat-icon"><i class="bi bi-patch-check-fill"></i></div>
            </div>
            <div class="stat-value"><?= number_format($checkedIn) ?></div>
            <div class="stat-label">Checked In</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card stat-card--red">
            <div class="stat-card-header">
                <div class="stat-icon"><i class="bi bi-person-x-fill"></i></div>
            </div>
            <div class="stat-value"><?= number_format($noShows) ?></div>
            <div class="stat-label">No-Shows</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card stat-card--gold">
            <div class="stat-card-header">
                <div class="stat-icon"><i class="bi bi-graph-up-arrow"></i></div>
            </div>
            <div class="stat-value"><?= $attendancePct ?>%</div>
            <div class="stat-label">Attendance Rate</div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <!-- Daily Registrations -->
    <div class="col-lg-8">
        <div class="ems-card">
            <div class="card-header">
                <div>
                    <div class="card-title">Daily Registrations</div>
                    <div class="card-subtitle">Last 30 days</div>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($daily)): ?>
                    <div class="empty-state" style="padding:40px 0">
                        <div class="empty-icon"><i class="bi bi-bar-chart"></i></div>
                        <div class="empty-title">No data yet</div>
                    </div>
                <?php else: ?>
                    <canvas id="dailyChart" height="90"></canvas>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Gender Doughnut -->
    <div class="col-lg-4">
        <div class="ems-card">
            <div class="card-header">
                <div class="card-title">Gender Breakdown</div>
            </div>
            <div class="card-body">
                <?php if (empty($genders)): ?>
                    <div class="empty-state" style="padding:40px 0">
                        <div class="empty-icon"><i class="bi bi-pie-chart"></i></div>
                        <div class="empty-title">No data</div>
                    </div>
                <?php else: ?>
                    <canvas id="genderChart" height="190"></canvas>
                    <div class="mt-3">
                        <?php foreach ($genders as $g): ?>
                        <div class="d-flex justify-content-between align-items-center py-1" style="border-bottom:1px solid var(--border-light)">
                            <span style="font-size:13px;font-weight:500;color:var(--text-secondary);text-transform:capitalize"><?= $e(str_replace('_',' ',$g['gender'])) ?></span>
                            <span style="font-size:13px;font-weight:700;color:var(--text-primary)"><?= number_format($g['total']) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Tables Row -->
<div class="row g-4">
    <!-- Top Churches -->
    <div class="col-lg-6">
        <div class="ems-card">
            <div class="card-header">
                <div class="card-title"><i class="bi bi-building me-2" style="color:var(--ems-gold)"></i>Top Churches</div>
            </div>
            <?php if (empty($churches)): ?>
                <div class="card-body"><div class="empty-state" style="padding:32px 0"><div class="empty-title">No data</div></div></div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="ems-table">
                    <thead><tr><th>#</th><th>Church / Organisation</th><th class="text-end">Count</th></tr></thead>
                    <tbody>
                        <?php foreach ($churches as $i => $c): ?>
                        <tr>
                            <td style="width:36px">
                                <?php if ($i < 3): ?>
                                    <span style="width:24px;height:24px;border-radius:50%;background:<?= ['#f59e0b','#94a3b8','#cd7f32'][$i] ?>;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:#fff"><?= $i+1 ?></span>
                                <?php else: ?>
                                    <span class="cell-muted"><?= $i+1 ?></span>
                                <?php endif; ?>
                            </td>
                            <td><span class="cell-primary"><?= $e($c['church_name'] ?: 'Not Specified') ?></span></td>
                            <td class="text-end"><span class="ems-badge ems-badge--blue"><?= number_format($c['total']) ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Top States -->
    <div class="col-lg-6">
        <div class="ems-card">
            <div class="card-header">
                <div class="card-title"><i class="bi bi-map me-2" style="color:var(--ems-gold)"></i>Top States</div>
            </div>
            <?php if (empty($states)): ?>
                <div class="card-body"><div class="empty-state" style="padding:32px 0"><div class="empty-title">No data</div></div></div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="ems-table">
                    <thead><tr><th>#</th><th>State</th><th class="text-end">Count</th></tr></thead>
                    <tbody>
                        <?php foreach ($states as $i => $s): ?>
                        <tr>
                            <td style="width:36px">
                                <?php if ($i < 3): ?>
                                    <span style="width:24px;height:24px;border-radius:50%;background:<?= ['#f59e0b','#94a3b8','#cd7f32'][$i] ?>;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:#fff"><?= $i+1 ?></span>
                                <?php else: ?>
                                    <span class="cell-muted"><?= $i+1 ?></span>
                                <?php endif; ?>
                            </td>
                            <td><span class="cell-primary"><?= $e($s['state'] ?: 'Not Specified') ?></span></td>
                            <td class="text-end"><span class="ems-badge ems-badge--purple"><?= number_format($s['total']) ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (!empty($daily) || !empty($genders)): ?>
<script>
Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
Chart.defaults.color = '#94a3b8';

<?php if (!empty($daily)): ?>
const dailyCtx = document.getElementById('dailyChart').getContext('2d');
new Chart(dailyCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($daily,'day')) ?>,
        datasets: [{
            label: 'Registrations',
            data: <?= json_encode(array_column($daily,'total')) ?>,
            backgroundColor: 'rgba(30,58,95,.7)',
            borderColor: '#1e3a5f',
            borderWidth: 0,
            borderRadius: 5,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ' '+ctx.raw+' registrations' } } },
        scales: {
            y: { beginAtZero:true, ticks:{ stepSize:1, font:{size:11} }, grid:{ color:'#f0f4f8' } },
            x: { ticks:{ font:{size:10}, maxRotation:45 }, grid:{ display:false } }
        }
    }
});
<?php endif; ?>

<?php if (!empty($genders)): ?>
const genderCtx = document.getElementById('genderChart').getContext('2d');
new Chart(genderCtx, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_map(fn($g) => ucfirst(str_replace('_',' ',$g['gender'])), $genders)) ?>,
        datasets: [{
            data: <?= json_encode(array_column($genders,'total')) ?>,
            backgroundColor: ['#1e3a5f','#c9963a','#94a3b8','#10b981'],
            borderWidth: 0,
            hoverOffset: 6,
        }]
    },
    options: {
        responsive: true, cutout: '68%',
        plugins: { legend: { position:'bottom', labels:{ font:{size:12}, padding:12 } } }
    }
});
<?php endif; ?>
</script>
<?php endif; ?>
