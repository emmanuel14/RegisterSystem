<?php
$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$pageTitle = $event['title'];
$B = fn(string $p = '') => Helpers\Helper::base($p);
?>

<!-- ── Event Hero ─────────────────────────────────────────────── -->
<div class="event-hero">
    <?php if ($event['banner_image']): ?>
        <div class="event-hero-bg" style="background-image:url('<?= $B('uploads/banners/' . $e($event['banner_image'])) ?>')"></div>
    <?php else: ?>
        <div class="event-hero-bg" style="background:linear-gradient(135deg,#0d1b2e,#1a3c5e)"></div>
    <?php endif; ?>
    <div class="event-hero-overlay"></div>

    <div class="container event-hero-content">
        <a href="<?= $B() ?>" style="color:rgba(255,255,255,.6);text-decoration:none;font-size:13px;font-weight:500;display:inline-flex;align-items:center;gap:6px;margin-bottom:16px;transition:.2s">
            <i class="bi bi-arrow-left"></i> All Events
        </a>

        <div class="event-hero-eyebrow">
            <i class="bi bi-calendar3"></i>
            <?= Helpers\Helper::formatDate($event['start_date'], 'l, F j, Y') ?>
        </div>

        <h1 class="event-hero-title"><?= $e($event['title']) ?></h1>

        <?php if ($event['theme']): ?>
            <p class="event-hero-theme">"<?= $e($event['theme']) ?>"</p>
        <?php endif; ?>

        <div class="event-hero-meta">
            <?php if ($event['venue']): ?>
            <div class="event-hero-meta-item">
                <i class="bi bi-geo-alt-fill"></i>
                <span><?= $e($event['venue']) ?><?= $event['city'] ? ', ' . $e($event['city']) : '' ?></span>
            </div>
            <?php endif; ?>
            <div class="event-hero-meta-item">
                <i class="bi bi-clock-fill"></i>
                <span><?= Helpers\Helper::formatDate($event['start_date'], 'g:i A') ?> – <?= Helpers\Helper::formatDate($event['end_date'], 'g:i A') ?></span>
            </div>
            <?php if ($event['capacity']): ?>
            <div class="event-hero-meta-item">
                <i class="bi bi-people-fill"></i>
                <span>Capacity: <?= number_format($event['capacity']) ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ── Content ────────────────────────────────────────────────── -->
<section class="pub-section-sm" style="background:var(--body-bg)">
    <div class="container">
        <div class="row g-5 align-items-start">

            <!-- Left: Content -->
            <div class="col-lg-7">

                <!-- About -->
                <div style="background:#fff;border:1px solid var(--border);border-radius:var(--r-xl);padding:32px;margin-bottom:24px;box-shadow:var(--shadow-sm)">
                    <h2 style="font-family:'Playfair Display',Georgia,serif;font-size:22px;font-weight:800;color:var(--text-primary);margin-bottom:16px">About This Event</h2>
                    <?php if ($event['description']): ?>
                        <div style="line-height:1.8;color:#374151;font-size:15px"><?= nl2br($e($event['description'])) ?></div>
                    <?php else: ?>
                        <p style="color:var(--text-muted)">No description available.</p>
                    <?php endif; ?>
                </div>

                <!-- Speakers -->
                <?php if ($speakers): ?>
                <div style="background:#fff;border:1px solid var(--border);border-radius:var(--r-xl);padding:32px;margin-bottom:24px;box-shadow:var(--shadow-sm)">
                    <h2 style="font-family:'Playfair Display',Georgia,serif;font-size:22px;font-weight:800;color:var(--text-primary);margin-bottom:20px">
                        <i class="bi bi-mic-fill" style="font-size:18px;color:var(--pub-gold);margin-right:8px"></i>Speakers
                    </h2>
                    <div class="row g-3">
                        <?php foreach ($speakers as $sp): ?>
                        <div class="col-sm-6">
                            <div class="speaker-card">
                                <div class="speaker-avatar"><?= $e(Helpers\Helper::avatarInitials($sp['name'])) ?></div>
                                <div>
                                    <div class="speaker-name"><?= $e($sp['name']) ?></div>
                                    <?php if ($sp['title']): ?>
                                        <div class="speaker-title"><?= $e($sp['title']) ?></div>
                                    <?php endif; ?>
                                    <?php if ($sp['bio']): ?>
                                        <div class="speaker-bio"><?= $e($sp['bio']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Schedule -->
                <?php if ($schedule): ?>
                <div style="background:#fff;border:1px solid var(--border);border-radius:var(--r-xl);padding:32px;box-shadow:var(--shadow-sm)">
                    <h2 style="font-family:'Playfair Display',Georgia,serif;font-size:22px;font-weight:800;color:var(--text-primary);margin-bottom:20px">
                        <i class="bi bi-list-ol" style="font-size:18px;color:var(--pub-gold);margin-right:8px"></i>Programme Schedule
                    </h2>
                    <?php
                    $byDay = [];
                    foreach ($schedule as $item) { $byDay[$item['day']][] = $item; }
                    ?>
                    <?php foreach ($byDay as $day => $items): ?>
                        <div class="schedule-day-header">
                            <h6><?= Helpers\Helper::formatDate($day, 'l, F j, Y') ?></h6>
                        </div>
                        <?php foreach ($items as $item): ?>
                        <div class="schedule-item">
                            <div class="schedule-time"><?= $e(substr($item['start_time'],0,5)) ?><?= $item['end_time'] ? '–'.substr($item['end_time'],0,5) : '' ?></div>
                            <div class="schedule-dot"></div>
                            <div style="flex:1">
                                <div class="schedule-title"><?= $e($item['title']) ?></div>
                                <?php if ($item['description']): ?><div class="schedule-desc"><?= $e($item['description']) ?></div><?php endif; ?>
                                <?php if ($item['speaker_name']): ?><div class="schedule-speaker"><i class="bi bi-person me-1"></i><?= $e($item['speaker_name']) ?></div><?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Right: Registration Sidebar -->
            <div class="col-lg-5">
                <div class="event-sidebar-card">
                    <div class="event-sidebar-header">
                        <?php if ($registrationOpen): ?>
                            <div class="reg-open-badge"><i class="bi bi-circle-fill" style="font-size:8px"></i> Registration Open</div>
                        <?php else: ?>
                            <div class="reg-open-badge" style="background:rgba(239,68,68,.2);border-color:rgba(239,68,68,.3);color:#fca5a5">
                                <i class="bi bi-x-circle"></i> Registration Closed
                            </div>
                        <?php endif; ?>
                        <h3><?= $e($event['title']) ?></h3>
                        <?php if ($event['theme']): ?>
                            <p>"<?= $e($event['theme']) ?>"</p>
                        <?php endif; ?>
                    </div>

                    <div class="event-sidebar-body">
                        <div class="event-detail-list">
                            <div class="event-detail-item">
                                <div class="event-detail-icon"><i class="bi bi-calendar3"></i></div>
                                <div>
                                    <div class="event-detail-label">Date</div>
                                    <div class="event-detail-value"><?= Helpers\Helper::formatDate($event['start_date'], 'l, M j, Y') ?></div>
                                    <?php if (Helpers\Helper::formatDate($event['start_date'],'Y-m-d') !== Helpers\Helper::formatDate($event['end_date'],'Y-m-d')): ?>
                                        <div style="font-size:12px;color:var(--text-muted)">to <?= Helpers\Helper::formatDate($event['end_date'],'l, M j, Y') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="event-detail-item">
                                <div class="event-detail-icon"><i class="bi bi-clock"></i></div>
                                <div>
                                    <div class="event-detail-label">Time</div>
                                    <div class="event-detail-value"><?= Helpers\Helper::formatDate($event['start_date'],'g:i A') ?></div>
                                </div>
                            </div>
                            <?php if ($event['venue']): ?>
                            <div class="event-detail-item">
                                <div class="event-detail-icon"><i class="bi bi-geo-alt-fill"></i></div>
                                <div>
                                    <div class="event-detail-label">Venue</div>
                                    <div class="event-detail-value"><?= $e($event['venue']) ?></div>
                                    <?php if ($event['venue_address']): ?><div style="font-size:12px;color:var(--text-muted)"><?= $e($event['venue_address']) ?></div><?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if ($event['capacity']): ?>
                            <div class="event-detail-item">
                                <div class="event-detail-icon"><i class="bi bi-people-fill"></i></div>
                                <div>
                                    <div class="event-detail-label">Capacity</div>
                                    <div class="event-detail-value"><?= number_format($event['capacity']) ?> seats</div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if ($event['registration_close']): ?>
                            <div class="event-detail-item">
                                <div class="event-detail-icon"><i class="bi bi-hourglass-split"></i></div>
                                <div>
                                    <div class="event-detail-label">Closes</div>
                                    <div class="event-detail-value"><?= Helpers\Helper::formatDateTime($event['registration_close']) ?></div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="mt-4">
                            <?php if ($registrationOpen): ?>
                                <a href="<?= $B('events/' . $e($event['slug']) . '/register') ?>" class="pub-btn pub-btn-gold w-100 justify-content-center" style="border-radius:10px;padding:14px;font-size:15px">
                                    <i class="bi bi-pencil-square"></i> Register Now — It's Free
                                </a>
                                <p style="text-align:center;font-size:12px;color:var(--text-muted);margin-top:10px">
                                    <i class="bi bi-shield-check me-1"></i>Your information is safe and secure
                                </p>
                            <?php else: ?>
                                <div style="text-align:center;padding:16px;background:#f8fafc;border-radius:10px;border:1px solid var(--border)">
                                    <i class="bi bi-lock-fill" style="font-size:24px;color:var(--text-muted)"></i>
                                    <p style="margin:8px 0 0;color:var(--text-secondary);font-size:13.5px">Registration is currently closed for this event.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
