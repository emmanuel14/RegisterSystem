<?php
$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$pageTitle = 'Upcoming Events';
$B = fn(string $p = '') => Helpers\Helper::base($p);
?>

<!-- ── Hero ──────────────────────────────────────────────────── -->
<section class="pub-hero">
    <div class="container" style="position:relative">
        <div class="pub-hero-eyebrow">
            <i class="bi bi-stars"></i>
            <?= $e($settings['church_name'] ?? 'Our Events') ?>
        </div>
        <h1 class="pub-hero-title">
            Discover &amp; Register<br>
            <span>for Our Events</span>
        </h1>
        <p class="pub-hero-sub">
            Browse our upcoming programmes, conferences, and special services.
            Register online and receive your digital pass instantly.
        </p>
        <div class="pub-hero-actions">
            <a href="#events" class="pub-btn pub-btn-gold pub-btn-lg">
                <i class="bi bi-calendar3"></i> Browse Events
            </a>
            <a href="#events" class="pub-btn pub-btn-outline pub-btn-lg">
                <i class="bi bi-info-circle"></i> Learn More
            </a>
        </div>
    </div>
</section>

<!-- ── Stats bar ──────────────────────────────────────────────── -->
<?php if (!empty($events)): ?>
<div style="background:#fff;border-bottom:1px solid #e2e8f0">
    <div class="container">
        <div class="row g-0">
            <?php
            $totalRegs = array_sum(array_column($events, 'reg_count'));
            $openCount = count(array_filter($events, fn($e) => $e['registration_status'] === 'open'));
            ?>
            <div class="col-4 text-center" style="padding:18px 0;border-right:1px solid #e2e8f0">
                <div style="font-size:22px;font-weight:800;color:var(--pub-primary)"><?= count($events) ?></div>
                <div style="font-size:12px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.4px">Events</div>
            </div>
            <div class="col-4 text-center" style="padding:18px 0;border-right:1px solid #e2e8f0">
                <div style="font-size:22px;font-weight:800;color:#10b981"><?= $openCount ?></div>
                <div style="font-size:12px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.4px">Open Now</div>
            </div>
            <div class="col-4 text-center" style="padding:18px 0">
                <div style="font-size:22px;font-weight:800;color:var(--pub-gold)"><?= number_format($totalRegs) ?></div>
                <div style="font-size:12px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.4px">Registered</div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ── Events Grid ────────────────────────────────────────────── -->
<section class="pub-section" id="events">
    <div class="container">
        <div class="text-center mb-5">
            <div class="section-eyebrow"><i class="bi bi-calendar3"></i> Upcoming Events</div>
            <h2 class="section-title">Our Latest Programmes</h2>
            <p class="section-subtitle mx-auto">
                All events are open to the public. Register today to secure your spot and receive your digital entry pass.
            </p>
        </div>

        <?php if (empty($events)): ?>
            <div class="text-center py-5">
                <div style="width:80px;height:80px;border-radius:20px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:32px;color:#94a3b8;margin:0 auto 16px">
                    <i class="bi bi-calendar-x"></i>
                </div>
                <h3 style="font-size:20px;font-weight:700;color:#1e293b;margin-bottom:8px">No Events Available</h3>
                <p style="color:#64748b;max-width:340px;margin:0 auto">There are no published events at the moment. Please check back soon for upcoming programmes.</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($events as $ev): ?>
                <div class="col-sm-6 col-lg-4">
                    <div class="event-card">
                        <!-- Image -->
                        <div class="event-card-img-wrap">
                            <?php if ($ev['banner_image']): ?>
                                <img src="<?= $B('uploads/banners/' . $e($ev['banner_image'])) ?>"
                                     class="event-card-img" alt="<?= $e($ev['title']) ?>">
                            <?php else: ?>
                                <div class="event-card-img--placeholder">
                                    <i class="bi bi-calendar-event"></i>
                                </div>
                            <?php endif; ?>

                            <?php if ($ev['is_featured']): ?>
                                <span class="event-card-badge">Featured</span>
                            <?php endif; ?>

                            <?php
                            $isOpen   = $ev['registration_status'] === 'open' && (!$ev['capacity'] || $ev['reg_count'] < $ev['capacity']);
                            $isFull   = $ev['capacity'] && $ev['reg_count'] >= $ev['capacity'];
                            ?>
                            <span class="event-status-badge <?= $isOpen ? 'open' : 'closed' ?>">
                                <?= $isFull ? 'Full' : ($isOpen ? 'Open' : 'Closed') ?>
                            </span>
                        </div>

                        <!-- Body -->
                        <div class="event-card-body">
                            <div class="event-card-date">
                                <i class="bi bi-calendar3"></i>
                                <?= Helpers\Helper::formatDate($ev['start_date']) ?>
                                <?php if (Helpers\Helper::formatDate($ev['start_date']) !== Helpers\Helper::formatDate($ev['end_date'])): ?>
                                    &mdash; <?= Helpers\Helper::formatDate($ev['end_date']) ?>
                                <?php endif; ?>
                            </div>

                            <h3 class="event-card-title"><?= $e($ev['title']) ?></h3>

                            <?php if ($ev['theme']): ?>
                                <p class="event-card-theme">"<?= $e($ev['theme']) ?>"</p>
                            <?php endif; ?>

                            <div class="event-card-meta">
                                <?php if ($ev['venue']): ?>
                                <div class="event-meta-item">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    <span><?= $e($ev['venue']) ?><?= $ev['city'] ? ', ' . $e($ev['city']) : '' ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="event-meta-item">
                                    <i class="bi bi-clock"></i>
                                    <span><?= Helpers\Helper::formatDate($ev['start_date'], 'g:i A') ?></span>
                                </div>
                                <?php if ($ev['capacity']): ?>
                                <div class="event-meta-item">
                                    <i class="bi bi-people"></i>
                                    <span><?= number_format($ev['reg_count']) ?> / <?= number_format($ev['capacity']) ?> registered</span>
                                </div>
                                <?php endif; ?>
                            </div>

                            <div class="event-card-footer">
                                <?php if ($isOpen): ?>
                                    <span style="font-size:12px;font-weight:600;color:var(--green);display:flex;align-items:center;gap:5px">
                                        <span style="width:7px;height:7px;border-radius:50%;background:var(--green);display:inline-block;animation:pulse 1.5s infinite"></span>
                                        Registration Open
                                    </span>
                                <?php elseif ($isFull): ?>
                                    <span style="font-size:12px;font-weight:600;color:var(--red)"><i class="bi bi-x-circle me-1"></i>Fully Booked</span>
                                <?php else: ?>
                                    <span style="font-size:12px;font-weight:600;color:var(--text-muted)"><i class="bi bi-lock me-1"></i>Closed</span>
                                <?php endif; ?>

                                <a href="<?= $B('events/' . $e($ev['slug'])) ?>" class="pub-btn pub-btn-primary pub-btn-sm">
                                    View Details <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ── CTA Banner ─────────────────────────────────────────────── -->
<section style="background:linear-gradient(135deg,var(--pub-primary),#1a4a7a);padding:64px 0;margin-bottom:-80px">
    <div class="container text-center">
        <h2 style="font-family:'Playfair Display',Georgia,serif;font-size:clamp(22px,4vw,36px);font-weight:800;color:#fff;margin-bottom:12px">
            Ready to be a part of something great?
        </h2>
        <p style="color:rgba(255,255,255,.7);font-size:16px;margin-bottom:28px;max-width:460px;margin-left:auto;margin-right:auto">
            Register for any of our upcoming events and receive your digital pass immediately.
        </p>
        <a href="#events" class="pub-btn pub-btn-gold pub-btn-lg">
            <i class="bi bi-arrow-up"></i> Browse All Events
        </a>
    </div>
</section>

<style>
@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%       { opacity: .6; transform: scale(1.3); }
}
</style>
