<?php
$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$pageTitle = 'Registrations';
$B = fn(string $p = '') => Helpers\Helper::base($p);
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Registrations</h1>
        <p class="page-subtitle"><?= number_format($total) ?> record<?= $total !== 1 ? 's' : '' ?></p>
    </div>
    <div class="page-actions">
        <a href="<?= $B('admin/registrations/export-csv?' . http_build_query(array_filter(['event_id'=>$filters['event_id']]))) ?>"
           class="btn btn-outline-secondary">
            <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV
        </a>
    </div>
</div>

<!-- Filters -->
<div class="filter-bar mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-sm-6 col-md-3">
            <div style="position:relative">
                <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:13px"></i>
                <input type="text" name="search" class="form-control" style="padding-left:36px"
                       placeholder="Name, email, code…" value="<?= $e($filters['search']) ?>">
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <select name="event_id" class="form-select">
                <option value="">All Events</option>
                <?php foreach ($events as $ev): ?>
                    <option value="<?= $ev['id'] ?>" <?= (string)$filters['event_id']===(string)$ev['id']?'selected':'' ?>>
                        <?= $e(Helpers\Helper::truncate($ev['title'], 40)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-sm-4 col-md-2">
            <select name="gender" class="form-select">
                <option value="">Gender</option>
                <option value="male"   <?= $filters['gender']==='male'  ?'selected':'' ?>>Male</option>
                <option value="female" <?= $filters['gender']==='female'?'selected':'' ?>>Female</option>
                <option value="other"  <?= $filters['gender']==='other' ?'selected':'' ?>>Other</option>
            </select>
        </div>
        <div class="col-sm-4 col-md-2">
            <select name="checked_in" class="form-select">
                <option value="">Check-in</option>
                <option value="1" <?= $filters['checked_in']==='1'?'selected':'' ?>>Checked In</option>
                <option value="0" <?= $filters['checked_in']==='0'?'selected':'' ?>>Not Yet</option>
            </select>
        </div>
        <div class="col-sm-4 col-md-auto">
            <button class="btn btn-primary"><i class="bi bi-search"></i> Filter</button>
            <a href="<?= $B('admin/registrations') ?>" class="btn btn-outline-secondary ms-1"><i class="bi bi-x"></i></a>
        </div>
    </form>
</div>

<div class="ems-card">
    <?php if (empty($regs)): ?>
        <div class="card-body">
            <div class="empty-state">
                <div class="empty-icon"><i class="bi bi-inbox"></i></div>
                <div class="empty-title">No registrations found</div>
                <div class="empty-desc">Try adjusting your filters or share an event link to start collecting registrations.</div>
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
                    <th>Church</th>
                    <th>Gender</th>
                    <th>Registered</th>
                    <th class="text-center">Check-In</th>
                    <th class="text-end" style="padding-right:20px">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($regs as $r): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar avatar-sm"><?= $e(Helpers\Helper::avatarInitials($r['first_name'].' '.$r['last_name'])) ?></div>
                            <div>
                                <div class="cell-primary"><?= $e($r['first_name'].' '.$r['last_name']) ?></div>
                                <div class="cell-muted"><?= $e($r['email']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td><span class="cell-muted" style="max-width:160px;display:inline-block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= $e($r['event_title']) ?></span></td>
                    <td><span class="reg-code"><?= $e($r['registration_code']) ?></span></td>
                    <td><span class="cell-muted"><?= $e($r['church_name'] ?: '—') ?></span></td>
                    <td>
                        <span class="ems-badge ems-badge--gray text-capitalize"><?= $e($r['gender']) ?></span>
                    </td>
                    <td><span class="cell-muted"><?= Helpers\Helper::formatDate($r['registered_at'],'M j, Y') ?></span></td>
                    <td class="text-center">
                        <?php if ($r['checked_in_at']): ?>
                            <span class="ems-badge ems-badge--green"><i class="bi bi-check-lg"></i> Done</span>
                        <?php else: ?>
                            <span class="ems-badge ems-badge--gray">Pending</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end" style="padding-right:14px">
                        <div class="d-flex align-items-center justify-content-end gap-1">
                            <a href="<?= $B('admin/registrations/'.$r['id']) ?>" class="btn btn-icon btn-sm btn-outline-secondary" title="View"><i class="bi bi-eye"></i></a>
                            <a href="<?= $B('admin/registrations/'.$r['id'].'/edit') ?>" class="btn btn-icon btn-sm btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                            <a href="<?= $B('admin/registrations/'.$r['id'].'/print') ?>" target="_blank" class="btn btn-icon btn-sm btn-outline-secondary" title="Print"><i class="bi bi-printer"></i></a>
                            <button class="btn btn-icon btn-sm btn-outline-danger btn-delete-reg"
                                    data-id="<?= $r['id'] ?>" data-name="<?= $e($r['first_name'].' '.$r['last_name']) ?>" title="Delete">
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
    <?php $q = http_build_query(array_filter(array_merge($filters,['page'=>null]))); ?>
    <a href="?<?= $q ?>&page=<?= $paging['current']-1 ?>" class="<?= !$paging['has_prev']?'disabled':'' ?>"><i class="bi bi-chevron-left"></i></a>
    <?php for($i=max(1,$paging['current']-2);$i<=min($paging['total_pages'],$paging['current']+2);$i++): ?>
        <a href="?<?= $q ?>&page=<?= $i ?>" class="<?= $i===$paging['current']?'active':'' ?>"><?= $i ?></a>
    <?php endfor; ?>
    <a href="?<?= $q ?>&page=<?= $paging['current']+1 ?>" class="<?= !$paging['has_next']?'disabled':'' ?>"><i class="bi bi-chevron-right"></i></a>
</div>
<?php endif; ?>

<script>
const CSRF = '<?= $e($csrf) ?>';
const BASE = '<?= $B() ?>';
document.querySelectorAll('.btn-delete-reg').forEach(btn => {
    btn.addEventListener('click', async function () {
        const {isConfirmed} = await Swal.fire({
            icon:'warning', title:'Delete Registration?',
            html:`Remove registration for <strong>${this.dataset.name}</strong>?`,
            showCancelButton:true, confirmButtonText:'Delete', confirmButtonColor:'#ef4444'
        });
        if (!isConfirmed) return;
        const res  = await fetch(BASE+'admin/registrations/'+this.dataset.id+'/delete',{
            method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:'<?= CSRF_TOKEN_NAME ?>='+CSRF
        });
        const data = await res.json();
        if (data.success) { Swal.fire({icon:'success',title:'Deleted',timer:1200,showConfirmButton:false}); setTimeout(()=>location.reload(),1300); }
        else { Swal.fire({icon:'error',title:'Error',text:data.message}); }
    });
});
</script>
