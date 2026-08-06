<?php
$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$pageTitle = 'Events';
?>

<div class="page-header">
    <div>
        <h2 class="page-title">Events</h2>
        <p class="page-subtitle"><?= number_format($total) ?> event<?= $total !== 1 ? 's' : '' ?> total</p>
    </div>
    <div class="page-actions">
        <a href="<?= Helpers\Helper::base('admin/events/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> New Event
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card ems-card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-sm-6 col-md-4">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Search events…" value="<?= $e($filters['search']) ?>">
            </div>
            <div class="col-sm-4 col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <?php foreach (['draft','published','cancelled','completed'] as $s): ?>
                        <option value="<?= $s ?>" <?= $filters['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-sm btn-primary"><i class="bi bi-search"></i> Filter</button>
                <a href="<?= Helpers\Helper::base('admin/events') ?>" class="btn btn-sm btn-outline-secondary ms-1">Clear</a>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card ems-card">
    <div class="card-body p-0">
        <?php if (empty($events)): ?>
            <div class="empty-state py-5">
                <i class="bi bi-calendar-x fs-1 text-muted"></i>
                <p class="text-muted mt-2">No events found.</p>
                <a href="<?= Helpers\Helper::base('admin/events/create') ?>" class="btn btn-primary btn-sm mt-1">Create First Event</a>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover ems-table mb-0">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Dates</th>
                        <th>Venue</th>
                        <th class="text-center">Regs</th>
                        <th>Status</th>
                        <th>Registration</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $ev): ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?= $e($ev['title']) ?></div>
                            <?php if ($ev['theme']): ?>
                                <small class="text-muted"><?= $e($ev['theme']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="small"><?= Helpers\Helper::formatDate($ev['start_date']) ?></div>
                            <div class="small text-muted">to <?= Helpers\Helper::formatDate($ev['end_date']) ?></div>
                        </td>
                        <td class="small text-muted"><?= $e($ev['venue'] ?: '—') ?></td>
                        <td class="text-center">
                            <span class="badge bg-secondary"><?= number_format($ev['reg_count']) ?></span>
                            <?php if ($ev['capacity']): ?>
                                <div class="small text-muted">/ <?= number_format($ev['capacity']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $statusColors = ['published'=>'success','draft'=>'secondary','cancelled'=>'danger','completed'=>'info'];
                            $sc = $statusColors[$ev['status']] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?= $sc ?>"><?= ucfirst($e($ev['status'])) ?></span>
                        </td>
                        <td>
                            <?php $rc = $ev['registration_status'] === 'open' ? 'success' : 'danger'; ?>
                            <span class="badge bg-<?= $rc ?>"><?= ucfirst($e($ev['registration_status'])) ?></span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="<?= Helpers\Helper::base('admin/events/' . $ev['id']) ?>" class="btn btn-outline-secondary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?= Helpers\Helper::base('admin/events/' . $ev['id'] . '/edit') ?>" class="btn btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button class="btn btn-outline-<?= $ev['status'] === 'published' ? 'warning' : 'success' ?> btn-toggle-status"
                                        data-id="<?= $ev['id'] ?>"
                                        data-status="<?= $e($ev['status']) ?>"
                                        title="<?= $ev['status'] === 'published' ? 'Unpublish' : 'Publish' ?>">
                                    <i class="bi bi-<?= $ev['status'] === 'published' ? 'eye-slash' : 'eye' ?>"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-delete-event"
                                        data-id="<?= $ev['id'] ?>"
                                        data-name="<?= $e($ev['title']) ?>"
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
        <li class="page-item <?= !$paging['has_prev'] ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $paging['current'] - 1 ?>&search=<?= $e($filters['search']) ?>&status=<?= $e($filters['status']) ?>">«</a>
        </li>
        <?php for ($i = 1; $i <= $paging['total_pages']; $i++): ?>
            <li class="page-item <?= $i === $paging['current'] ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>&search=<?= $e($filters['search']) ?>&status=<?= $e($filters['status']) ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?= !$paging['has_next'] ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $paging['current'] + 1 ?>&search=<?= $e($filters['search']) ?>&status=<?= $e($filters['status']) ?>">»</a>
        </li>
    </ul>
</nav>
<?php endif; ?>

<script>
const CSRF = '<?= $e($csrf) ?>';

// Toggle publish status
document.querySelectorAll('.btn-toggle-status').forEach(btn => {
    btn.addEventListener('click', async function () {
        const id     = this.dataset.id;
        const status = this.dataset.status;
        const action = status === 'published' ? 'unpublish' : 'publish';

        const res = await fetch(`/admin/events/${id}/toggle-status`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `<?= CSRF_TOKEN_NAME ?>=` + CSRF
        });
        const data = await res.json();
        if (data.success) {
            Swal.fire({ icon: 'success', title: 'Updated!', text: data.message, timer: 1500, showConfirmButton: false });
            setTimeout(() => location.reload(), 1600);
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: data.message });
        }
    });
});

// Delete event
document.querySelectorAll('.btn-delete-event').forEach(btn => {
    btn.addEventListener('click', async function () {
        const id   = this.dataset.id;
        const name = this.dataset.name;

        const confirmed = await Swal.fire({
            icon: 'warning', title: 'Delete Event?',
            html: `Are you sure you want to delete <strong>${name}</strong>? This cannot be undone.`,
            showCancelButton: true, confirmButtonColor: '#dc3545',
            confirmButtonText: 'Yes, Delete', cancelButtonText: 'Cancel'
        });

        if (!confirmed.isConfirmed) return;

        const res  = await fetch(`/admin/events/${id}/delete`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `<?= CSRF_TOKEN_NAME ?>=` + CSRF
        });
        const data = await res.json();
        if (data.success) {
            Swal.fire({ icon: 'success', title: 'Deleted!', text: data.message, timer: 1500, showConfirmButton: false });
            setTimeout(() => location.reload(), 1600);
        } else {
            Swal.fire({ icon: 'error', title: 'Cannot Delete', text: data.message });
        }
    });
});
</script>
