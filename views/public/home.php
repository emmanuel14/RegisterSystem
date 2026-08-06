<?php
$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$pageTitle = 'Events';
?>

<!-- Hero -->
<section class="pub-hero">
    <div class="container">
        <h1 class="pub-hero-title">Upcoming Events</h1>
        <p class="pub-hero-sub">Register for any of our upcoming events below</p>
    </div>
</section>

<div class="container py-5">
    <?php if (empty($events)): ?>
        <div class="text-center py-5">
            <i class="bi bi-calendar-x" style="font-size:4rem;color:#ccc"></i>
            <h3 class="mt-3 text-muted">No Events Available</h3>
            <p class="text-muted">Check back soon for upcoming events.</p>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($events as $ev): ?>
            <div class="col-sm-6 col-lg-4">
                <div class="pub-event-card">
                    <?php if ($ev['banner_image']): ?>
                        <img src="/uploads/banners/<?= $e($ev['banner_image']) ?>" class="pub-event-img" alt="<?= $e($ev['title']) ?>">
                    <?php else: ?>
                        <div class="pub-event-img pub-event-img--placeholder">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                    <?php endif; ?>

                    <?php if ($ev['is_featured']): ?>
                        <span class="pub-badge pub-badge--featured">Featured</span>
                    <?php endif; ?>

                    <div class="pub-event-body">
                        <div class="pub-event-date">
                            <i class="bi bi-calendar3 me-1"></i>
                            <?= Helpers\Helper::formatDate($ev['start_date']) ?>
                            <?php if (Helpers\Helper::formatDate($ev['start_date']) !== Helpers\Helper::formatDate($ev['end_date'])): ?>
                                – <?= Helpers\Helper::formatDate($ev['end_date']) ?>
                            <?php endif; ?>
                        </div>

                        <h3 class="pub-event-title"><?= $e($ev['title']) ?></h3>

                        <?php if ($ev['theme']): ?>
                            <p class="pub-event-theme"><?= $e($ev['theme']) ?></p>
                        <?php endif; ?>

                        <?php if ($ev['venue']): ?>
                            <div class="pub-event-venue">
                                <i class="bi bi-geo-alt me-1"></i>
                                <?= $e($ev['venue']) ?><?= $ev['city'] ? ', ' . $e($ev['city']) : '' ?>
                            </div>
                        <?php endif; ?>

                        <div class="pub-event-footer">
                            <?php if ($ev['capacity'] && $ev['reg_count'] >= $ev['capacity']): ?>
                                <span class="badge bg-danger">Fully Booked</span>
                            <?php elseif ($ev['registration_status'] === 'open'): ?>
                                <span class="badge bg-success">Registration Open</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Registration Closed</span>
                            <?php endif; ?>

                            <a href="<?= Helpers\Helper::base('events/' . $e($ev['slug'])) ?>" class="btn btn-sm pub-btn-primary">
                                View Details <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
