<?php
$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$pageTitle = 'Register — ' . $event['title'];
$B = fn(string $p = '') => Helpers\Helper::base($p);
$saved = \Helpers\Session::get('reg_form_data', []);
\Helpers\Session::delete('reg_form_data');
$nigerianStates = ['Abia','Adamawa','Akwa Ibom','Anambra','Bauchi','Bayelsa','Benue','Borno','Cross River','Delta','Ebonyi','Edo','Ekiti','Enugu','FCT','Gombe','Imo','Jigawa','Kaduna','Kano','Katsina','Kebbi','Kogi','Kwara','Lagos','Nasarawa','Niger','Ogun','Ondo','Osun','Oyo','Plateau','Rivers','Sokoto','Taraba','Yobe','Zamfara'];
?>

<!-- Header -->
<div class="reg-page-header">
    <div class="container">
        <a href="<?= $B('events/' . $e($event['slug'])) ?>" style="color:rgba(255,255,255,.6);text-decoration:none;font-size:13px;display:inline-flex;align-items:center;gap:6px;margin-bottom:16px">
            <i class="bi bi-arrow-left"></i> Back to Event
        </a>
        <h1 style="font-family:'Playfair Display',Georgia,serif;font-size:clamp(22px,4vw,36px);font-weight:800;color:#fff;margin-bottom:6px"><?= $e($event['title']) ?></h1>
        <p style="color:rgba(255,255,255,.65);font-size:14px;margin:0">
            <i class="bi bi-calendar3 me-2"></i><?= Helpers\Helper::formatDate($event['start_date']) ?>
            <?php if ($event['venue']): ?>&nbsp;·&nbsp;<i class="bi bi-geo-alt me-1"></i><?= $e($event['venue']) ?><?php endif; ?>
        </p>
    </div>
</div>

<!-- Form -->
<div style="background:var(--body-bg);padding:40px 0 60px">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">

                <!-- Error Alert -->
                <?php
                use Helpers\Session;
                $flashErrors = Session::getFlash('error');
                if ($flashErrors): ?>
                <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:14px 18px;display:flex;align-items:flex-start;gap:10px;margin-bottom:20px;color:#991b1b">
                    <i class="bi bi-exclamation-circle-fill flex-shrink-0 mt-1"></i>
                    <div style="font-size:13.5px"><?= is_array($flashErrors) ? implode('<br>', array_map($e, $flashErrors)) : $e($flashErrors) ?></div>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?= $B('events/' . $e($event['slug']) . '/register') ?>" novalidate id="regForm">
                    <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= $e($csrf) ?>">

                    <div class="reg-form-card">

                        <!-- Section 1: Personal -->
                        <div class="reg-form-section">
                            <div class="reg-section-title">
                                <span class="reg-section-icon"><i class="bi bi-person"></i></span>
                                Personal Information
                            </div>
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="pub-form-label">First Name <span style="color:var(--red)">*</span></label>
                                    <input type="text" name="first_name" class="pub-form-control" value="<?= $e($saved['first_name'] ?? '') ?>" required autocomplete="given-name" placeholder="John">
                                </div>
                                <div class="col-sm-6">
                                    <label class="pub-form-label">Last Name <span style="color:var(--red)">*</span></label>
                                    <input type="text" name="last_name" class="pub-form-control" value="<?= $e($saved['last_name'] ?? '') ?>" required autocomplete="family-name" placeholder="Doe">
                                </div>
                                <div class="col-sm-6">
                                    <label class="pub-form-label">Email Address <span style="color:var(--red)">*</span></label>
                                    <input type="email" name="email" class="pub-form-control" value="<?= $e($saved['email'] ?? '') ?>" required autocomplete="email" placeholder="john@example.com">
                                </div>
                                <div class="col-sm-6">
                                    <label class="pub-form-label">Phone Number <span style="color:var(--red)">*</span></label>
                                    <input type="tel" name="phone" class="pub-form-control" value="<?= $e($saved['phone'] ?? '') ?>" required autocomplete="tel" placeholder="+234 xxx xxx xxxx">
                                </div>
                                <div class="col-sm-6">
                                    <label class="pub-form-label">Gender <span style="color:var(--red)">*</span></label>
                                    <select name="gender" class="pub-form-control" required>
                                        <option value="">Select gender</option>
                                        <?php foreach (['male'=>'Male','female'=>'Female','other'=>'Other','prefer_not_to_say'=>'Prefer not to say'] as $v => $l): ?>
                                            <option value="<?= $v ?>" <?= ($saved['gender'] ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label class="pub-form-label">Date of Birth <span style="color:var(--text-muted);font-weight:400">(optional)</span></label>
                                    <input type="date" name="date_of_birth" class="pub-form-control" value="<?= $e($saved['date_of_birth'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Church -->
                        <div class="reg-form-section">
                            <div class="reg-section-title">
                                <span class="reg-section-icon"><i class="bi bi-building"></i></span>
                                Church / Organisation
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="pub-form-label">Church / Organisation Name <span style="color:var(--red)">*</span></label>
                                    <input type="text" name="church_name" class="pub-form-control" value="<?= $e($saved['church_name'] ?? '') ?>" required placeholder="e.g. RCCG Victory Parish">
                                </div>
                                <div class="col-sm-6">
                                    <label class="pub-form-label">State <span style="color:var(--red)">*</span></label>
                                    <select name="state" class="pub-form-control" required>
                                        <option value="">Select State</option>
                                        <?php foreach ($nigerianStates as $s): ?>
                                            <option value="<?= $s ?>" <?= ($saved['state'] ?? '') === $s ? 'selected' : '' ?>><?= $s ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label class="pub-form-label">City <span style="color:var(--red)">*</span></label>
                                    <input type="text" name="city" class="pub-form-control" value="<?= $e($saved['city'] ?? '') ?>" required placeholder="e.g. Port Harcourt">
                                </div>
                                <div class="col-12">
                                    <label class="pub-form-label">Address <span style="color:var(--text-muted);font-weight:400">(optional)</span></label>
                                    <textarea name="address" class="pub-form-control" rows="2" placeholder="Street address..."><?= $e($saved['address'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Emergency -->
                        <div class="reg-form-section">
                            <div class="reg-section-title">
                                <span class="reg-section-icon"><i class="bi bi-telephone-plus"></i></span>
                                Emergency Contact
                                <span style="font-size:11px;color:var(--text-muted);font-weight:400;text-transform:none;letter-spacing:0">Optional</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="pub-form-label">Contact Name</label>
                                    <input type="text" name="emergency_contact_name" class="pub-form-control" value="<?= $e($saved['emergency_contact_name'] ?? '') ?>" placeholder="Full name">
                                </div>
                                <div class="col-sm-6">
                                    <label class="pub-form-label">Contact Phone</label>
                                    <input type="tel" name="emergency_contact_phone" class="pub-form-control" value="<?= $e($saved['emergency_contact_phone'] ?? '') ?>" placeholder="+234 xxx xxx xxxx">
                                </div>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="reg-form-section" style="background:#f8fafc">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <div style="font-size:12.5px;color:var(--text-muted)">
                                    <i class="bi bi-shield-check me-1" style="color:var(--green)"></i>
                                    Your data is protected. We will send your QR pass to your email.
                                </div>
                                <button type="submit" class="pub-btn pub-btn-gold pub-btn-lg" id="submitBtn">
                                    <i class="bi bi-send"></i> Submit Registration
                                </button>
                            </div>
                        </div>
                    </div><!-- /reg-form-card -->
                </form>
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
