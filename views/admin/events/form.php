<?php
$e         = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$isEdit    = !empty($event);
$pageTitle = $isEdit ? 'Edit Event' : 'Create Event';
$action    = $isEdit ? Helpers\Helper::base('admin/events/' . $event['id'] . '/update') : Helpers\Helper::base('admin/events');
?>

<div class="page-header">
    <div>
        <h2 class="page-title"><?= $isEdit ? 'Edit Event' : 'New Event' ?></h2>
        <p class="page-subtitle"><?= $isEdit ? 'Update event details' : 'Fill in the details below' ?></p>
    </div>
    <div class="page-actions">
        <a href="<?= Helpers\Helper::base('admin/events') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<form method="POST" action="<?= $action ?>" enctype="multipart/form-data" id="eventForm">
    <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $e($csrf) ?>">
    <input type="hidden" name="speakers_json" id="speakersJson" value="<?= $e(json_encode(array_map(fn($s) => ['name'=>$s['name'],'title'=>$s['title'],'bio'=>$s['bio']], $speakers ?? []))) ?>">
    <input type="hidden" name="schedule_json" id="scheduleJson" value="<?= $e(json_encode($schedule ?? [])) ?>">

    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-8">

            <!-- Basic Info -->
            <div class="card ems-card mb-4">
                <div class="card-header"><h5 class="card-title mb-0">Basic Information</h5></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Event Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="titleInput" class="form-control"
                               value="<?= $e($event['title'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Theme / Tagline</label>
                        <input type="text" name="theme" class="form-control" value="<?= $e($event['theme'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">URL Slug</label>
                        <div class="input-group">
                            <span class="input-group-text text-muted small">/events/</span>
                            <input type="text" name="slug" id="slugInput" class="form-control"
                                   value="<?= $e($event['slug'] ?? '') ?>" placeholder="auto-generated">
                        </div>
                        <div class="form-text">Leave blank to auto-generate from title.</div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" rows="6" class="form-control"
                                  placeholder="Full event description…"><?= $e($event['description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Date & Location -->
            <div class="card ems-card mb-4">
                <div class="card-header"><h5 class="card-title mb-0">Date & Location</h5></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Start Date & Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="start_date" class="form-control"
                                   value="<?= $e(isset($event['start_date']) ? date('Y-m-d\TH:i', strtotime($event['start_date'])) : '') ?>" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">End Date & Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="end_date" class="form-control"
                                   value="<?= $e(isset($event['end_date']) ? date('Y-m-d\TH:i', strtotime($event['end_date'])) : '') ?>" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Registration Opens</label>
                            <input type="datetime-local" name="registration_open" class="form-control"
                                   value="<?= $e(isset($event['registration_open']) && $event['registration_open'] ? date('Y-m-d\TH:i', strtotime($event['registration_open'])) : '') ?>">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Registration Closes</label>
                            <input type="datetime-local" name="registration_close" class="form-control"
                                   value="<?= $e(isset($event['registration_close']) && $event['registration_close'] ? date('Y-m-d\TH:i', strtotime($event['registration_close'])) : '') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Venue / Location</label>
                            <input type="text" name="venue" class="form-control" value="<?= $e($event['venue'] ?? '') ?>" placeholder="Church hall, conference center…">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Venue Address</label>
                            <textarea name="venue_address" class="form-control" rows="2"><?= $e($event['venue_address'] ?? '') ?></textarea>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">City</label>
                            <input type="text" name="city" class="form-control" value="<?= $e($event['city'] ?? '') ?>">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">State</label>
                            <input type="text" name="state" class="form-control" value="<?= $e($event['state'] ?? '') ?>">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label fw-semibold">Country</label>
                            <input type="text" name="country" class="form-control" value="<?= $e($event['country'] ?? 'Nigeria') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Speakers -->
            <div class="card ems-card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Speakers</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addSpeaker">
                        <i class="bi bi-plus"></i> Add Speaker
                    </button>
                </div>
                <div class="card-body">
                    <div id="speakersList">
                        <!-- Speakers rendered by JS -->
                    </div>
                    <p class="text-muted small mb-0" id="noSpeakers">No speakers added yet.</p>
                </div>
            </div>

            <!-- Schedule -->
            <div class="card ems-card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Schedule</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addSchedule">
                        <i class="bi bi-plus"></i> Add Item
                    </button>
                </div>
                <div class="card-body">
                    <div id="scheduleList"></div>
                    <p class="text-muted small mb-0" id="noSchedule">No schedule items added yet.</p>
                </div>
            </div>

        </div>

        <!-- Right Column -->
        <div class="col-lg-4">

            <!-- Publish Settings -->
            <div class="card ems-card mb-4">
                <div class="card-header"><h5 class="card-title mb-0">Publish Settings</h5></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Event Status</label>
                        <select name="status" class="form-select">
                            <?php foreach (['draft','published','cancelled','completed'] as $s): ?>
                                <option value="<?= $s ?>" <?= ($event['status'] ?? 'draft') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Registration Status</label>
                        <select name="registration_status" class="form-select">
                            <option value="open"     <?= ($event['registration_status'] ?? 'open') === 'open'     ? 'selected' : '' ?>>Open</option>
                            <option value="closed"   <?= ($event['registration_status'] ?? '') === 'closed'   ? 'selected' : '' ?>>Closed</option>
                            <option value="waitlist" <?= ($event['registration_status'] ?? '') === 'waitlist' ? 'selected' : '' ?>>Waitlist</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Capacity</label>
                        <input type="number" name="capacity" class="form-control" min="1"
                               value="<?= $e($event['capacity'] ?? '') ?>" placeholder="Leave blank for unlimited">
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="is_featured" id="isFeatured"
                               <?= !empty($event['is_featured']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="isFeatured">Feature this event</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="allow_walk_in" id="allowWalkIn"
                               <?= !empty($event['allow_walk_in']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="allowWalkIn">Allow walk-in check-in</label>
                    </div>
                </div>
            </div>

            <!-- Banner Image -->
            <div class="card ems-card mb-4">
                <div class="card-header"><h5 class="card-title mb-0">Banner Image</h5></div>
                <div class="card-body">
                    <?php if (!empty($event['banner_image'])): ?>
                        <img src="/uploads/banners/<?= $e($event['banner_image']) ?>"
                             class="img-fluid rounded mb-3" alt="Current Banner">
                    <?php endif; ?>
                    <input type="file" name="banner_image" class="form-control"
                           accept="image/jpeg,image/png,image/webp">
                    <div class="form-text">JPEG, PNG or WebP — max 5 MB. Recommended: 1200×600px.</div>
                </div>
            </div>

            <!-- Save -->
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-<?= $isEdit ? 'save' : 'plus-circle' ?> me-2"></i>
                    <?= $isEdit ? 'Save Changes' : 'Create Event' ?>
                </button>
                <?php if ($isEdit): ?>
                    <a href="<?= Helpers\Helper::base('events/' . $e($event['slug'] ?? '')) ?>" target="_blank" class="btn btn-outline-secondary">
                        <i class="bi bi-eye me-1"></i> View on Site
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</form>

<script>
// ── Slug Auto-generate ────────────────────────────────────────────────────
const titleInput = document.getElementById('titleInput');
const slugInput  = document.getElementById('slugInput');
let slugManual   = slugInput.value.length > 0;

titleInput.addEventListener('input', function () {
    if (!slugManual) {
        slugInput.value = this.value.toLowerCase()
            .replace(/[^\w\s-]/g, '').replace(/[\s_]+/g, '-').replace(/^-+|-+$/g, '');
    }
});
slugInput.addEventListener('input', () => { slugManual = slugInput.value.length > 0; });

// ── Speakers ──────────────────────────────────────────────────────────────
let speakers = JSON.parse(document.getElementById('speakersJson').value || '[]');

function renderSpeakers() {
    const list = document.getElementById('speakersList');
    const none = document.getElementById('noSpeakers');
    list.innerHTML = '';
    none.style.display = speakers.length ? 'none' : 'block';

    speakers.forEach((s, i) => {
        list.innerHTML += `
        <div class="speaker-row border rounded p-3 mb-2">
            <div class="row g-2">
                <div class="col-sm-6">
                    <input type="text" class="form-control form-control-sm" placeholder="Name *"
                           value="${escHtml(s.name)}" onchange="speakers[${i}].name=this.value;syncSpeakers()">
                </div>
                <div class="col-sm-6">
                    <input type="text" class="form-control form-control-sm" placeholder="Title / Role"
                           value="${escHtml(s.title||'')}" onchange="speakers[${i}].title=this.value;syncSpeakers()">
                </div>
                <div class="col-12">
                    <textarea class="form-control form-control-sm" rows="2" placeholder="Short bio"
                              onchange="speakers[${i}].bio=this.value;syncSpeakers()">${escHtml(s.bio||'')}</textarea>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-link text-danger p-0 mt-2"
                    onclick="speakers.splice(${i},1);renderSpeakers()">
                <i class="bi bi-trash"></i> Remove
            </button>
        </div>`;
    });
}

function syncSpeakers() {
    document.getElementById('speakersJson').value = JSON.stringify(speakers);
}

document.getElementById('addSpeaker').addEventListener('click', () => {
    speakers.push({ name: '', title: '', bio: '' });
    renderSpeakers();
});

// ── Schedule ──────────────────────────────────────────────────────────────
let schedule = JSON.parse(document.getElementById('scheduleJson').value || '[]');

function renderSchedule() {
    const list = document.getElementById('scheduleList');
    const none = document.getElementById('noSchedule');
    list.innerHTML = '';
    none.style.display = schedule.length ? 'none' : 'block';

    schedule.forEach((item, i) => {
        list.innerHTML += `
        <div class="border rounded p-3 mb-2">
            <div class="row g-2">
                <div class="col-sm-3">
                    <input type="date" class="form-control form-control-sm" placeholder="Day"
                           value="${escHtml(item.day||'')}" onchange="schedule[${i}].day=this.value;syncSchedule()">
                </div>
                <div class="col-sm-2">
                    <input type="time" class="form-control form-control-sm" placeholder="Start"
                           value="${escHtml(item.start_time||'')}" onchange="schedule[${i}].start_time=this.value;syncSchedule()">
                </div>
                <div class="col-sm-2">
                    <input type="time" class="form-control form-control-sm" placeholder="End"
                           value="${escHtml(item.end_time||'')}" onchange="schedule[${i}].end_time=this.value;syncSchedule()">
                </div>
                <div class="col-sm-5">
                    <input type="text" class="form-control form-control-sm" placeholder="Title *"
                           value="${escHtml(item.title||'')}" onchange="schedule[${i}].title=this.value;syncSchedule()">
                </div>
                <div class="col-12">
                    <input type="text" class="form-control form-control-sm" placeholder="Description"
                           value="${escHtml(item.description||'')}" onchange="schedule[${i}].description=this.value;syncSchedule()">
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-link text-danger p-0 mt-2"
                    onclick="schedule.splice(${i},1);renderSchedule()">
                <i class="bi bi-trash"></i> Remove
            </button>
        </div>`;
    });
}

function syncSchedule() {
    document.getElementById('scheduleJson').value = JSON.stringify(schedule);
}

document.getElementById('addSchedule').addEventListener('click', () => {
    schedule.push({ day: '', start_time: '', end_time: '', title: '', description: '' });
    renderSchedule();
});

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Init
renderSpeakers();
renderSchedule();
</script>
