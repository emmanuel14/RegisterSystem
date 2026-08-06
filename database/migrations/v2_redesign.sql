-- ============================================================
-- EMS v2 Redesign — Database Migration
-- Run once: mysql -u root ems_db < database/migrations/v2_redesign.sql
-- ============================================================

USE `ems_db`;

-- Extend attendees for member profiles
ALTER TABLE `attendees`
    ADD COLUMN IF NOT EXISTS `member_id` VARCHAR(20) DEFAULT NULL AFTER `id`,
    ADD COLUMN IF NOT EXISTS `profile_photo` VARCHAR(255) DEFAULT NULL AFTER `member_id`,
    ADD UNIQUE KEY IF NOT EXISTS `uq_attendees_member_id` (`member_id`);

-- Ministries
CREATE TABLE IF NOT EXISTS `ministries` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `slug`          VARCHAR(100) NOT NULL,
    `name`          VARCHAR(150) NOT NULL,
    `tagline`       VARCHAR(255) DEFAULT NULL,
    `description`   TEXT DEFAULT NULL,
    `leader_name`   VARCHAR(150) DEFAULT NULL,
    `leader_title`  VARCHAR(150) DEFAULT NULL,
    `leader_photo`  VARCHAR(255) DEFAULT NULL,
    `cover_image`   VARCHAR(255) DEFAULT NULL,
    `icon`          VARCHAR(50) DEFAULT 'bi-people-fill',
    `color`         VARCHAR(20) DEFAULT '#1a3c5e',
    `is_active`     TINYINT(1) NOT NULL DEFAULT 1,
    `sort_order`    TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_ministries_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Announcements
CREATE TABLE IF NOT EXISTS `announcements` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title`         VARCHAR(255) NOT NULL,
    `content`       TEXT NOT NULL,
    `type`          ENUM('general','program','urgent') NOT NULL DEFAULT 'general',
    `event_id`      INT UNSIGNED DEFAULT NULL,
    `is_published`  TINYINT(1) NOT NULL DEFAULT 0,
    `published_at`  DATETIME DEFAULT NULL,
    `created_by`    INT UNSIGNED DEFAULT NULL,
    `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_ann_published` (`is_published`, `published_at`),
    CONSTRAINT `fk_ann_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Testimonials
CREATE TABLE IF NOT EXISTS `testimonials` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `attendee_id`   INT UNSIGNED DEFAULT NULL,
    `author_name`   VARCHAR(150) NOT NULL,
    `author_role`   VARCHAR(150) DEFAULT NULL,
    `content`       TEXT NOT NULL,
    `rating`        TINYINT UNSIGNED DEFAULT 5,
    `is_approved`   TINYINT(1) NOT NULL DEFAULT 0,
    `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_test_approved` (`is_approved`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Event gallery
CREATE TABLE IF NOT EXISTS `event_gallery` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `event_id`      INT UNSIGNED NOT NULL,
    `type`          ENUM('photo','video') NOT NULL DEFAULT 'photo',
    `title`         VARCHAR(255) DEFAULT NULL,
    `file_path`     VARCHAR(255) DEFAULT NULL,
    `video_url`     VARCHAR(500) DEFAULT NULL,
    `caption`       TEXT DEFAULT NULL,
    `sort_order`    SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_gallery_event` (`event_id`),
    CONSTRAINT `fk_gallery_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Notifications (admin dashboard + member)
CREATE TABLE IF NOT EXISTS `notifications` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `type`          VARCHAR(50) NOT NULL,
    `title`         VARCHAR(255) NOT NULL,
    `message`       TEXT NOT NULL,
    `recipient_type` ENUM('admin','attendee','all') NOT NULL DEFAULT 'attendee',
    `recipient_id`  INT UNSIGNED DEFAULT NULL,
    `is_read`       TINYINT(1) NOT NULL DEFAULT 0,
    `link`          VARCHAR(500) DEFAULT NULL,
    `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_notif_recipient` (`recipient_type`, `recipient_id`, `is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Homepage & contact settings
INSERT IGNORE INTO `settings` (`key`, `value`, `type`, `group`, `label`) VALUES
('hero_tagline',        'Welcome Home',                         'text',     'general',  'Hero Tagline'),
('hero_subtitle',       'Experience faith, community, and purpose together.', 'textarea', 'general', 'Hero Subtitle'),
('about_text',          'We are a vibrant community of believers committed to worship, fellowship, and making a difference in our world.', 'textarea', 'general', 'About Text'),
('contact_email',       'info@church.org',                      'email',    'general',  'Contact Email'),
('contact_phone',       '+234 800 000 0000',                    'text',     'general',  'Contact Phone'),
('contact_address',     '123 Faith Avenue, Lagos, Nigeria',     'text',     'general',  'Contact Address'),
('social_facebook',     '',                                     'text',     'general',  'Facebook URL'),
('social_instagram',    '',                                     'text',     'general',  'Instagram URL'),
('social_youtube',      '',                                     'text',     'general',  'YouTube URL'),
('hero_image',          '',                                     'text',     'general',  'Hero Background Image URL');

-- Seed ministries
INSERT IGNORE INTO `ministries` (`slug`, `name`, `tagline`, `description`, `leader_name`, `leader_title`, `icon`, `color`, `sort_order`) VALUES
('youth',       'Youth Ministry',       'Raising the next generation of leaders', 'Dynamic programs for teens and young adults focused on faith, leadership, and community impact.', 'Pastor David Okonkwo', 'Youth Pastor', 'bi-lightning-fill', '#2563eb', 1),
('women',       'Women Ministry',       'Empowered women, transformed lives', 'A sisterhood of faith where women grow spiritually, build friendships, and serve together.', 'Mrs. Grace Adeyemi', 'Women Leader', 'bi-heart-fill', '#db2777', 2),
('men',         'Men Ministry',         'Strong men, strong families', 'Brotherhood gatherings, mentorship, and accountability for men of all ages.', 'Bro. Michael Eze', 'Men Coordinator', 'bi-shield-fill', '#1a3c5e', 3),
('children',    'Children Ministry',    'Building faith from the ground up', 'Age-appropriate worship and learning for children in a safe, fun environment.', 'Sis. Faith Johnson', 'Children Director', 'bi-stars', '#f59e0b', 4),
('choir',       'Choir & Worship',      'Leading hearts into His presence', 'Our worship team and choir minister through music that uplifts and transforms.', 'Min. Samuel Ade', 'Worship Leader', 'bi-music-note-beamed', '#7c3aed', 5);

-- Seed testimonials
INSERT IGNORE INTO `testimonials` (`author_name`, `author_role`, `content`, `rating`, `is_approved`) VALUES
('Sarah M.', 'Member since 2019', 'This church has truly become my second home. The programs are well-organized and the community is incredibly welcoming.', 5, 1),
('James O.', 'Conference Attendee', 'The registration process was seamless. I received my QR ticket instantly and check-in on event day took seconds.', 5, 1),
('Blessing A.', 'Youth Leader', 'Every program feels premium and thoughtfully planned. Our youth are always excited for the next event.', 5, 1);
