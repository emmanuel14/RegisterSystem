<?php
$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$pageTitle = 'Edit Registration';
$nigerianStates = ['Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno','Cross River','Delta','Ebonyi','Edo','Ekiti','Enugu','FCT','Gombe','Imo','Jigawa','Kaduna','Kano','Katsina','Kebbi','Kogi','Kwara','Lagos','Nasarawa','Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers','Sokoto','Taraba','Yobe','Zamfara'];
?>

<div class="page-header">
    <div>
        <h2 class="page-title">Edit Registration</h2>
        <p class="page-subtitle"><code><?= $e($reg['registration_code']) ?></code></p>
    </div>
    <div class="page-actions">
        <a href="<?= Helpers\Helper::base('admin/registrations/' . $reg['id']) ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<form method="POST" action="<?= Helpers\Helper::base('admin/registrations/' . $reg['id'] . '/update') ?>">
    <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $e($csrf) ?>">

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card ems-card">
                <div class="card-header"><h5 class="card-title mb-0">Attendee Details</h5></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" value="<?= $e($reg['first_name']) ?>" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" value="<?= $e($reg['last_name']) ?>" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="<?= $e($reg['email']) ?>" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Phone <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control" value="<?= $e($reg['phone']) ?>" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-select" required>
                                <?php foreach (['male'=>'Male','female'=>'Female','other'=>'Other','prefer_not_to_say'=>'Prefer not to say'] as $val => $label): ?>
                                    <option value="<?= $val ?>" <?= $reg['gender'] === $val ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Church / Organisation</label>
                            <input type="text" name="church_name" class="form-control" value="<?= $e($reg['church_name']) ?>">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">State</label>
                            <select name="state" class="form-select">
                                <option value="">Select State</option>
                                <?php foreach ($nigerianStates as $s): ?>
                                    <option value="<?= $s ?>" <?= $reg['state'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">City</label>
                            <input type="text" name="city" class="form-control" value="<?= $e($reg['city']) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Address</label>
                            <textarea name="address" class="form-control" rows="2"><?= $e($reg['address']) ?></textarea>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Emergency Contact Name</label>
                            <input type="text" name="emergency_contact_name" class="form-control" value="<?= $e($reg['emergency_contact_name']) ?>">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Emergency Contact Phone</label>
                            <input type="tel" name="emergency_contact_phone" class="form-control" value="<?= $e($reg['emergency_contact_phone']) ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card ems-card mb-4">
                <div class="card-header"><h5 class="card-title mb-0">Registration Status</h5></div>
                <div class="card-body">
                    <select name="status" class="form-select">
                        <?php foreach (['confirmed'=>'Confirmed','pending'=>'Pending','cancelled'=>'Cancelled','waitlisted'=>'Waitlisted'] as $val => $label): ?>
                            <option value="<?= $val ?>" <?= $reg['status'] === $val ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-save me-1"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
</form>
