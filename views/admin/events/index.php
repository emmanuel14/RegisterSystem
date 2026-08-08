<?php
$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$pageTitle = 'Events';
$B = fn(string $p = '') => Helpers\Helper::base($p);
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Events</h1>
        <p class="page-subtitle"><?= number_format($total) ?> event<?= $total !== 1 ? 's' : '' ?> total</p>
    </div>
    <div class="page-actions">
        <a href="<?= $B('admin/events/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> New Event
        </a>
    </div>
</div>

<!-- Filters -->
<div class="filter-bar mb-5">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-sm-5 col-md-4">
            <div style="position:relative">
                <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted)"></i>
                <input type="text" name="search" class="form-control" style="padding-left:36px"
                       placeholder="Search events…" value="<?= $e($filters['search']) ?>">
            </div>
        </div>
        <div class="col-sm-4 col-md-3">
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <?php foreach (['draft'=>'Draft','published'=>'Published','cancelled'=>'Cancelled','completed'=>'Completed'] as $v => $l): ?>
                    <option value="<?= $v ?>" <?= $filters['status'] === $v ? 'selected' : '' ?>><?= $l ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary"><i class="bi bi-search"></i> Filter</button>
            <a href="<?= $B('admin/events') ?>" class="btn btn-outline-secondary ms-1"><i class="bi bi-x"></i> Clear</a>
        </div>
    </form>
</div>

<div class="ems-card">
    <?php if (empty($events)): ?>
        <div class="card-body">
            <div class="empty-state">
                <div class="empty-icon"><i class="bi bi-calendar-x"></i></div>
                <div class="empty-title">No events found</div>
                <div class="empty-desc">Create your first event to get started.</div>
                <a href="<?= $B('admin/events/create') ?>" class="btn btn-primary btn-sm">Create Event</a>
            </div>
        </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="ems-table">
            <thead>
                <tr>
                    <th>Event</th>
                    <th>Dates</th>
                    <th>Venue</th>
                    <th class="text-center">Registrations</th>
                    <th>Status</th>
                    <th>Registration</th>
                    <th class="text-end" style="padding-right:22px">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $ev): ?>
                <tr>
                    <td>
                        <div class="cell-primary"><?= $e($ev['title']) ?></div>
                        <?php if ($ev['theme']): ?>
                            <div class="cell-muted" style="font-style:italic;font-size:12px">"<?= $e($ev['theme']) ?>"</div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="font-size:13px;font-weight:600"><?= Helpers\Helper::formatDate($ev['start_date'],'M j, Y') ?></div>
                        <div class="cell-muted">to <?= Helpers\Helper::formatDate($ev['end_date'],'M j, Y') ?></div>
                    </td>
                    <td><span class="cell-muted"><?= $e($ev['venue'] ?: '—') ?></span></td>
                    <td class="text-center">
                        <span style="font-size:16px;font-weight:800;color:var(--text-primary)"><?= number_format($ev['reg_count']) ?></span>
                        <?php if ($ev['capacity']): ?>
                            <div class="cell-muted" style="font-size:11px">/ <?= number_format($ev['capacity']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        $sc = ['published'=>'green','draft'=>'gray','cancelled'=>'red','completed'=>'blue'][$ev['status']] ?? 'gray';
                        ?>
                        <span class="ems-badge ems-badge--<?= $sc ?>"><?= ucfirst($e($ev['status'])) ?></span>
                    </td>
                    <td>
                        <span class="ems-badge ems-badge--<?= $ev['registration_status']==='open'?'green':'red' ?>">
                            <?= ucfirst($e($ev['registration_status'])) ?>
                        </span>
                    </td>
                    <td class="text-end" style="padding-right:16px">
                        <div class="d-flex align-items-center justify-content-end gap-1">
                            <a href="<?= $B('admin/events/' . $ev['id']) ?>" class="btn btn-icon btn-sm btn-outline-secondary" title="View"><i class="bi bi-eye"></i></a>
                            <a href="<?= $B('admin/events/' . $ev['id'] . '/edit') ?>" class="btn btn-icon btn-sm btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                            <button class="btn btn-icon btn-sm btn-outline-secondary btn-toggle-status"
                                    data-id="<?= $ev['id'] ?>" data-status="<?= $e($ev['status']) ?>"
                                    title="<?= $ev['status'] === 'published' ? 'Unpublish' : 'Publish' ?>">
                                <i class="bi bi-<?= $ev['status'] === 'published' ? 'eye-slash' : 'eye' ?>"></i>
                            </button>
                            <button class="btn btn-icon btn-sm btn-outline-danger btn-delete-event"
                                    data-id="<?= $ev['id'] ?>" data-name="<?= $e($ev['title']) ?>" title="Delete">
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

<!-- Pagination -->
<?php if ($paging['total_pages'] > 1): ?>
<div class="ems-pagination">
    <?php
    $q = http_build_query(array_filter(['search'=>$filters['search'],'status'=>$filters['status']]));
    ?>
    <a href="?<?= $q ?>&page=<?= $paging['current']-1 ?>" class="<?= !$paging['has_prev']?'disabled':'' ?>"><i class="bi bi-chevron-left"></i></a>
    <?php for($i=max(1,$paging['current']-2);$i<=min($paging['total_pages'],$paging['current']+2);$i++): ?>
        <a href="?<?= $q ?>&page=<?= $i ?>" class="<?= $i===$paging['current']?'active':'' ?>"><?= $i ?></a>
    <?php endfor; ?>
    <a href="?<?= $q ?>&page=<?= $paging['current']+1 ?>" class="<?= !$paging['has_next']?'disabled':'' ?>"><i class="bi bi-chevron-right"></i></a>
</div>
<?php endif; ?>

<script>
const CSRF = '<?= $e($csrf) ?>';
document.querySelectorAll('.btn-toggle-status').forEach(btn => {
    btn.addEventListener('click', async function () {
        const res  = await fetch(`<?= $B('admin/events/') ?>${this.dataset.id}/toggle-status`, {
            method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:'<?= CSRF_TOKEN_NAME ?>='+CSRF
        });
        const data = await res.json();
        if (data.success) {
            Swal.fire({icon:'success',title:'Updated',text:data.message,timer:1400,showConfirmButton:false});
            setTimeout(()=>location.reload(),1500);
        } else { Swal.fire({icon:'error',title:'Error',text:data.message}); }
    });
});
document.querySelectorAll('.btn-delete-event').forEach(btn => {
    btn.addEventListener('click', async function () {
        const {isConfirmed} = await Swal.fire({icon:'warning',title:'Delete Event?',
            html:`Delete <strong>${this.dataset.name}</strong>? This cannot be undone.`,
            showCancelButton:true,confirmButtonText:'Delete',confirmButtonColor:'#ef4444'});
        if (!isConfirmed) return;
        const res  = await fetch(`<?= $B('admin/events/') ?>${this.dataset.id}/delete`, {
            method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:'<?= CSRF_TOKEN_NAME ?>='+CSRF
        });
        const data = await res.json();
        if (data.success) { Swal.fire({icon:'success',title:'Deleted',timer:1200,showConfirmButton:false}); setTimeout(()=>location.reload(),1300); }
        else { Swal.fire({icon:'error',title:'Cannot Delete',text:data.message}); }
    });
});
</script>
