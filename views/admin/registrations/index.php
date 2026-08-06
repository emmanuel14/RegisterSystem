<?php
$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$pageTitle = 'Registrations';
?>

<div class="page-header">
    <div>
        <h2 class="page-title">Registrations</h2>
        <p class="page-subtitle"><?= number_format($total) ?> record<?= $total !== 1 ? 's' : '' ?></p>
    </div>
    <div class="page-actions d-flex gap-2 flex-wrap">
        <a href="<?= Helpers\Helper::base('admin/registrations/export-csv?' . http_build_query(array_filter(['event_id' => $filters['event_id']]))) ?>"
           class="btn btn-sm btn-outline-success">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export CSV
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card ems-card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-sm-6 col-md-3">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Name, email, code…" value="<?= $e($filters['search']) ?>">
            </div>
            <div class="col-sm-6 col-md-2">
                <select name="event_id" class="form-select form-select-sm">
                    <option value="">All Events</option>
                    <?php foreach ($events as $ev): ?>
                        <option value="<?= $ev['id'] ?>" <?= (string)$filters['event_id'] === (string)$ev['id'] ? 'selected' : '' ?>>
                            <?= $e(Helpers\Helper::truncate($ev['title'], 40)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-sm-4 col-md-2">
                <select name="gender" class="form-select form-select-sm">
                    <option value="">Gender</option>
                    <option value="male"   <?= $filters['gender']==='male'   ? 'selected' : '' ?>>Male</option>
                    <option value="female" <?= $filters['gender']==='female' ? 'selected' : '' ?>>Female</option>
                    <option value="other"  <?= $filters['gender']==='other'  ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            <div class="col-sm-4 col-md-2">
                <select name="checked_in" class="form-select form-select-sm">
                    <option value="">Check-in</option>
                    <option value="1" <?= $filters['checked_in']==='1' ? 'selected' : '' ?>>Checked In</option>
                    <option value="0" <?= $filters['checked_in']==='0' ? 'selected' : '' ?>>Not Checked In</option>
                </select>
            </div>
            <div class="col-sm-4 col-md-2">
                <input type="date" name="date" class="form-control form-control-sm" value="<?= $e($filters['date']) ?>">
            </div>
            <div class="col-auto">
                <button class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
                <a href="<?= Helpers\Helper::base('admin/registrations') ?>" class="btn btn-sm btn-outline-secondary ms-1">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card ems-card">
    <div class="card-body p-0">
        <?php if (empty($regs)): ?>
            <div class="empty-state py-5">
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <p class="text-muted mt-2">No registrations found.</p>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover ems-table mb-0">
                <thead>
                    <tr>
                        <th>Attendee</th>
                        <th>Event</th>
                        <th>Code</th>
                        <th>Church</th>
                        <th>Gender</th>
                        <th>Registered</th>
                        <th class="text-center">Check-in</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($regs as $r): ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?= $e($r['first_name'] . ' ' . $r['last_name']) ?></div>
                            <small class="text-muted"><?= $e($r['email']) ?></small>
                        </td>
                        <td><small><?= $e(Helpers\Helper::truncate($r['event_title'], 35)) ?></small></td>
                        <td><code class="reg-code small"><?= $e($r['registration_code']) ?></code></td>
                        <td><small class="text-muted"><?= $e($r['church_name'] ?: '—') ?></small></td>
                        <td><span class="badge bg-secondary text-capitalize"><?= $e($r['gender']) ?></span></td>
                        <td><small class="text-muted"><?= Helpers\Helper::formatDate($r['registered_at']) ?></small></td>
                        <td class="text-center">
                            <?php if ($r['checked_in_at']): ?>
                                <span class="badge bg-success"><i class="bi bi-check-lg"></i></span>
                            <?php else: ?>
                                <span class="badge bg-light text-secondary">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="<?= Helpers\Helper::base('admin/registrations/' . $r['id']) ?>" class="btn btn-outline-secondary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?= Helpers\Helper::base('admin/registrations/' . $r['id'] . '/edit') ?>" class="btn btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="<?= Helpers\Helper::base('admin/registrations/' . $r['id'] . '/print') ?>" target="_blank" class="btn btn-outline-secondary" title="Print">
                                    <i class="bi bi-printer"></i>
                                </a>
                                <button class="btn btn-outline-danger btn-delete-reg"
                                        data-id="<?= $r['id'] ?>"
                                        data-name="<?= $e($r['first_name'] . ' ' . $r['last_name']) ?>"
                                        title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Pagination -->
<?php if ($paging['total_pages'] > 1): ?>
<nav class="mt-4">
    <ul class="pagination pagination-sm justify-content-center">
        <?php
        $q = http_build_query(array_filter(array_merge($filters, ['page' => null])));
        ?>
        <li class="page-item <?= !$paging['has_prev'] ? 'disabled' : '' ?>">
            <a class="page-link" href="?<?= $q ?>&page=<?= $paging['current'] - 1 ?>">«</a>
        </li>
        <?php for ($i = max(1, $paging['current'] - 3); $i <= min($paging['total_pages'], $paging['current'] + 3); $i++): ?>
            <li class="page-item <?= $i === $paging['current'] ? 'active' : '' ?>">
                <a class="page-link" href="?<?= $q ?>&page=<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?= !$paging['has_next'] ? 'disabled' : '' ?>">
            <a class="page-link" href="?<?= $q ?>&page=<?= $paging['current'] + 1 ?>">»</a>
        </li>
    </ul>
</nav>
<?php endif; ?>

<script>
const CSRF = '<?= $e($csrf) ?>';
document.querySelectorAll('.btn-delete-reg').forEach(btn => {
    btn.addEventListener('click', async function () {
        const { isConfirmed } = await Swal.fire({
            icon: 'warning', title: 'Delete Registration?',
            html: `Remove registration for <strong>${this.dataset.name}</strong>?`,
            showCancelButton: true, confirmButtonColor: '#dc3545', confirmButtonText: 'Delete'
        });
        if (!isConfirmed) return;
        const res  = await fetch(`/admin/registrations/${this.dataset.id}/delete`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `<?= CSRF_TOKEN_NAME ?>=` + CSRF
        });
        const data = await res.json();
        if (data.success) {
            Swal.fire({ icon: 'success', title: 'Deleted', timer: 1200, showConfirmButton: false });
            setTimeout(() => location.reload(), 1300);
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: data.message });
        }
    });
});
</script>
