<?php
$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$pageTitle = $event['title'];
?>

<!-- Banner -->
<?php if ($event['banner_image']): ?>
<div class="pub-event-banner" style="background-image:url('/uploads/banners/<?= $e($event['banner_image']) ?>')">
    <div class="pub-event-banner-overlay">
        <div class="container">
            <h1 class="pub-event-hero-title"><?= $e($event['title']) ?></h1>
            <?php if ($event['theme']): ?>
                <p class="pub-event-hero-theme"><?= $e($event['theme']) ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php else: ?>
<div class="pub-hero">
    <div class="container">
        <h1 class="pub-hero-title"><?= $e($event['title']) ?></h1>
        <?php if ($event['theme']): ?><p class="pub-hero-sub"><?= $e($event['theme']) ?></p><?php endif; ?>
    </div>
</div>
<?php endif; ?>

<div class="container py-5">
    <div class="row g-5">
        <!-- Content -->
        <div class="col-lg-8">

            <!-- Description -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h3 class="fw-bold mb-3">About This Event</h3>
                    <?php if ($event['description']): ?>
                        <div class="pub-description"><?= nl2br($e($event['description'])) ?></div>
                    <?php else: ?>
                        <p class="text-muted">No description available.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Speakers -->
            <?php if ($speakers): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h3 class="fw-bold mb-4">Speakers</h3>
                    <div class="row g-4">
                        <?php foreach ($speakers as $sp): ?>
                        <div class="col-sm-6">
                            <div class="d-flex gap-3">
                                <div class="pub-speaker-avatar"><?= $e(Helpers\Helper::avatarInitials($sp['name'])) ?></div>
                                <div>
                                    <div class="fw-bold"><?= $e($sp['name']) ?></div>
                                    <?php if ($sp['title']): ?>
                                        <div class="small text-muted"><?= $e($sp['title']) ?></div>
                                    <?php endif; ?>
                                    <?php if ($sp['bio']): ?>
                                        <div class="small mt-1"><?= $e($sp['bio']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Schedule -->
            <?php if ($schedule): ?>
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h3 class="fw-bold mb-4">Programme</h3>
                    <?php
                    $byDay = [];
                    foreach ($schedule as $item) {
                        $byDay[$item['day']][] = $item;
                    }
                    ?>
                    <?php foreach ($byDay as $day => $items): ?>
                    <div class="mb-4">
                        <h5 class="pub-schedule-day"><?= Helpers\Helper::formatDate($day, 'l, F j, Y') ?></h5>
                        <?php foreach ($items as $item): ?>
                        <div class="pub-schedule-item">
                            <div class="pub-schedule-time">
                                <?= $e(substr($item['start_time'],0,5)) ?><?= $item['end_time'] ? '–' . substr($item['end_time'],0,5) : '' ?>
                            </div>
                            <div class="pub-schedule-content">
                                <div class="fw-semibold"><?= $e($item['title']) ?></div>
                                <?php if ($item['description']): ?>
                                    <div class="small text-muted"><?= $e($item['description']) ?></div>
                                <?php endif; ?>
                                <?php if ($item['speaker_name']): ?>
                                    <div class="small text-primary mt-1"><i class="bi bi-person me-1"></i><?= $e($item['speaker_name']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Register CTA -->
            <div class="card pub-reg-card mb-4">
                <div class="card-body p-4">
                    <?php if ($registrationOpen): ?>
                        <div class="text-center mb-3">
                            <span class="badge bg-success mb-2">Registration Open</span>
                            <p class="text-muted small mb-0">Secure your spot today</p>
                        </div>
                        <a href="<?= Helpers\Helper::base('events/' . $e($event['slug']) . '/register') ?>" class="btn pub-btn-primary w-100 btn-lg">
                            <i class="bi bi-pencil-square me-2"></i>Register Now
                        </a>
                        <?php if ($event['registration_close']): ?>
                            <div class="small text-muted text-center mt-2">
                                Closes: <?= Helpers\Helper::formatDateTime($event['registration_close']) ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($event['capacity']): ?>
                            <div class="small text-muted text-center mt-1">
                                Capacity: <?= number_format($event['capacity']) ?> attendees
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center">
                            <i class="bi bi-lock fs-1 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">Registration is currently closed.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Event Details -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Event Details</h5>
                    <ul class="list-unstyled pub-detail-list">
                        <li>
                            <i class="bi bi-calendar3"></i>
                            <div>
                                <strong>Date</strong><br>
                                <?= Helpers\Helper::formatDate($event['start_date'], 'l, F j, Y') ?>
                                <?php if (Helpers\Helper::formatDate($event['start_date'], 'Y-m-d') !== Helpers\Helper::formatDate($event['end_date'], 'Y-m-d')): ?>
                                    <br>to <?= Helpers\Helper::formatDate($event['end_date'], 'l, F j, Y') ?>
                                <?php endif; ?>
                            </div>
                        </li>
                        <li>
                            <i class="bi bi-clock"></i>
                            <div>
                                <strong>Time</strong><br>
                                <?= Helpers\Helper::formatDate($event['start_date'], 'g:i A') ?>
                            </div>
                        </li>
                        <?php if ($event['venue']): ?>
                        <li>
                            <i class="bi bi-geo-alt"></i>
                            <div>
                                <strong>Venue</strong><br>
                                <?= $e($event['venue']) ?>
                                <?php if ($event['venue_address']): ?><br><span class="text-muted small"><?= $e($event['venue_address']) ?></span><?php endif; ?>
                            </div>
                        </li>
                        <?php endif; ?>
                        <?php if ($event['city']): ?>
                        <li>
                            <i class="bi bi-pin-map"></i>
                            <div>
                                <strong>City</strong><br>
                                <?= $e($event['city']) ?><?= $event['state'] ? ', ' . $e($event['state']) : '' ?>
                            </div>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
