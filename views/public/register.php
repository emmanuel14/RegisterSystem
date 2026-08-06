<?php
$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$pageTitle = 'Register — ' . $event['title'];
$saved = \Helpers\Session::get('reg_form_data', []);
\Helpers\Session::delete('reg_form_data');

$nigerianStates = ['Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno','Cross River',
    'Delta','Ebonyi','Edo','Ekiti','Enugu','FCT','Gombe','Imo','Jigawa','Kaduna','Kano','Katsina','Kebbi',
    'Kogi','Kwara','Lagos','Nasarawa','Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers','Sokoto',
    'Taraba','Yobe','Zamfara'];
?>

<div class="pub-form-hero">
    <div class="container">
        <a href="<?= Helpers\Helper::base('events/' . $e($event['slug'])) ?>" class="text-white text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i> Back to Event
        </a>
        <h1 class="pub-hero-title mt-2"><?= $e($event['title']) ?></h1>
        <p class="pub-hero-sub">Complete the form below to register</p>
    </div>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <?php $flashErrors = \Helpers\Session::getFlash('error'); ?>
            <?php if ($flashErrors): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <?= implode('<br>', array_map($e, (array)$flashErrors)) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card border-0 shadow">
                <div class="card-body p-4 p-md-5">
                    <h3 class="fw-bold mb-1">Registration Form</h3>
                    <p class="text-muted mb-4">Fields marked with <span class="text-danger">*</span> are required.</p>

                    <form method="POST" action="<?= Helpers\Helper::base('events/' . $e($event['slug']) . '/register') ?>" novalidate id="regForm">
                        <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $e($csrf) ?>">

                        <!-- Personal Info -->
                        <h5 class="form-section-title">Personal Information</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control"
                                       value="<?= $e($saved['first_name'] ?? '') ?>" required autocomplete="given-name">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control"
                                       value="<?= $e($saved['last_name'] ?? '') ?>" required autocomplete="family-name">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control"
                                       value="<?= $e($saved['email'] ?? '') ?>" required autocomplete="email">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" name="phone" class="form-control"
                                       value="<?= $e($saved['phone'] ?? '') ?>" required autocomplete="tel"
                                       placeholder="+234 xxx xxx xxxx">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Gender <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select" required>
                                    <option value="">Select gender</option>
                                    <option value="male"              <?= ($saved['gender'] ?? '') === 'male'              ? 'selected' : '' ?>>Male</option>
                                    <option value="female"            <?= ($saved['gender'] ?? '') === 'female'            ? 'selected' : '' ?>>Female</option>
                                    <option value="other"             <?= ($saved['gender'] ?? '') === 'other'             ? 'selected' : '' ?>>Other</option>
                                    <option value="prefer_not_to_say" <?= ($saved['gender'] ?? '') === 'prefer_not_to_say' ? 'selected' : '' ?>>Prefer not to say</option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Date of Birth <span class="text-muted small">(optional)</span></label>
                                <input type="date" name="date_of_birth" class="form-control"
                                       value="<?= $e($saved['date_of_birth'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- Church Info -->
                        <h5 class="form-section-title">Church / Organisation</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label class="form-label">Church / Organisation Name <span class="text-danger">*</span></label>
                                <input type="text" name="church_name" class="form-control"
                                       value="<?= $e($saved['church_name'] ?? '') ?>" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">State <span class="text-danger">*</span></label>
                                <select name="state" class="form-select" required>
                                    <option value="">Select State</option>
                                    <?php foreach ($nigerianStates as $s): ?>
                                        <option value="<?= $s ?>" <?= ($saved['state'] ?? '') === $s ? 'selected' : '' ?>><?= $s ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">City <span class="text-danger">*</span></label>
                                <input type="text" name="city" class="form-control"
                                       value="<?= $e($saved['city'] ?? '') ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="2"><?= $e($saved['address'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <!-- Emergency Contact -->
                        <h5 class="form-section-title">Emergency Contact <span class="text-muted small fw-normal">(optional)</span></h5>
                        <div class="row g-3 mb-5">
                            <div class="col-sm-6">
                                <label class="form-label">Contact Name</label>
                                <input type="text" name="emergency_contact_name" class="form-control"
                                       value="<?= $e($saved['emergency_contact_name'] ?? '') ?>">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Contact Phone</label>
                                <input type="tel" name="emergency_contact_phone" class="form-control"
                                       value="<?= $e($saved['emergency_contact_phone'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn pub-btn-primary btn-lg" id="submitBtn">
                                <i class="bi bi-send me-2"></i> Submit Registration
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('regForm').addEventListener('submit', function () {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting…';
});
</script>
