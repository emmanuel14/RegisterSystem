-- ============================================================
-- Event Management System (EMS) - Database Schema
-- Version: 1.0.0
-- Engine: MySQL 8.0+ / MariaDB 10.6+
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS `ems_db`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `ems_db`;

-- ============================================================
-- TABLE: admins
-- Stores admin users with role-based access control
-- ============================================================
CREATE TABLE `admins` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`          VARCHAR(100) NOT NULL,
    `email`         VARCHAR(191) NOT NULL,
    `password`      VARCHAR(255) NOT NULL,
    `role`          ENUM('superadmin','admin','moderator','viewer') NOT NULL DEFAULT 'admin',
    `avatar`        VARCHAR(255) DEFAULT NULL,
    `is_active`     TINYINT(1) NOT NULL DEFAULT 1,
    `last_login`    DATETIME DEFAULT NULL,
    `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_admins_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: password_resets
-- ============================================================
CREATE TABLE `password_resets` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `email`         VARCHAR(191) NOT NULL,
    `token`         VARCHAR(255) NOT NULL,
    `expires_at`    DATETIME NOT NULL,
    `used`          TINYINT(1) NOT NULL DEFAULT 0,
    `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_pr_email` (`email`),
    KEY `idx_pr_token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: events
-- Core event data with full lifecycle management
-- ============================================================
CREATE TABLE `events` (
    `id`                    INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `slug`                  VARCHAR(255) NOT NULL,
    `title`                 VARCHAR(255) NOT NULL,
    `theme`                 VARCHAR(255) DEFAULT NULL,
    `description`           LONGTEXT DEFAULT NULL,
    `venue`                 VARCHAR(255) DEFAULT NULL,
    `venue_address`         TEXT DEFAULT NULL,
    `city`                  VARCHAR(100) DEFAULT NULL,
    `state`                 VARCHAR(100) DEFAULT NULL,
    `country`               VARCHAR(100) DEFAULT 'Nigeria',
    `start_date`            DATETIME NOT NULL,
    `end_date`              DATETIME NOT NULL,
    `registration_open`     DATETIME DEFAULT NULL,
    `registration_close`    DATETIME DEFAULT NULL,
    `capacity`              INT UNSIGNED DEFAULT NULL COMMENT 'NULL = unlimited',
    `banner_image`          VARCHAR(255) DEFAULT NULL,
    `status`                ENUM('draft','published','cancelled','completed') NOT NULL DEFAULT 'draft',
    `registration_status`   ENUM('open','closed','waitlist') NOT NULL DEFAULT 'open',
    `is_featured`           TINYINT(1) NOT NULL DEFAULT 0,
    `allow_walk_in`         TINYINT(1) NOT NULL DEFAULT 0,
    `meta_keywords`         TEXT DEFAULT NULL,
    `meta_description`      TEXT DEFAULT NULL,
    `created_by`            INT UNSIGNED NOT NULL,
    `created_at`            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_events_slug` (`slug`),
    KEY `idx_events_status` (`status`),
    KEY `idx_events_dates` (`start_date`, `end_date`),
    CONSTRAINT `fk_events_admin` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: event_speakers
-- ============================================================
CREATE TABLE `event_speakers` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `event_id`      INT UNSIGNED NOT NULL,
    `name`          VARCHAR(150) NOT NULL,
    `title`         VARCHAR(150) DEFAULT NULL,
    `bio`           TEXT DEFAULT NULL,
    `photo`         VARCHAR(255) DEFAULT NULL,
    `sort_order`    TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_es_event` (`event_id`),
    CONSTRAINT `fk_es_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: event_schedule
-- ============================================================
CREATE TABLE `event_schedule` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `event_id`      INT UNSIGNED NOT NULL,
    `day`           DATE NOT NULL,
    `start_time`    TIME NOT NULL,
    `end_time`      TIME DEFAULT NULL,
    `title`         VARCHAR(255) NOT NULL,
    `description`   TEXT DEFAULT NULL,
    `speaker_id`    INT UNSIGNED DEFAULT NULL,
    `location`      VARCHAR(255) DEFAULT NULL,
    `sort_order`    SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_esc_event` (`event_id`),
    CONSTRAINT `fk_esc_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_esc_speaker` FOREIGN KEY (`speaker_id`) REFERENCES `event_speakers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: attendees
-- Master attendee record (person, reusable across events)
-- ============================================================
CREATE TABLE `attendees` (
    `id`                    INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `first_name`            VARCHAR(80) NOT NULL,
    `last_name`             VARCHAR(80) NOT NULL,
    `email`                 VARCHAR(191) NOT NULL,
    `phone`                 VARCHAR(25) NOT NULL,
    `gender`                ENUM('male','female','other','prefer_not_to_say') NOT NULL,
    `date_of_birth`         DATE DEFAULT NULL,
    `church_name`           VARCHAR(200) DEFAULT NULL,
    `state`                 VARCHAR(100) DEFAULT NULL,
    `city`                  VARCHAR(100) DEFAULT NULL,
    `address`               TEXT DEFAULT NULL,
    `emergency_contact_name`    VARCHAR(150) DEFAULT NULL,
    `emergency_contact_phone`   VARCHAR(25) DEFAULT NULL,
    `created_at`            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`            DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_attendees_email` (`email`),
    KEY `idx_attendees_phone` (`phone`),
    KEY `idx_attendees_name` (`last_name`, `first_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: registrations
-- Links attendees to events; holds the registration code
-- ============================================================
CREATE TABLE `registrations` (
    `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `registration_code` VARCHAR(30) NOT NULL,
    `event_id`          INT UNSIGNED NOT NULL,
    `attendee_id`       INT UNSIGNED NOT NULL,
    `status`            ENUM('pending','confirmed','cancelled','waitlisted') NOT NULL DEFAULT 'confirmed',
    `notes`             TEXT DEFAULT NULL,
    `registered_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_reg_code` (`registration_code`),
    UNIQUE KEY `uq_reg_event_attendee` (`event_id`, `attendee_id`),
    KEY `idx_reg_event` (`event_id`),
    KEY `idx_reg_attendee` (`attendee_id`),
    KEY `idx_reg_status` (`status`),
    CONSTRAINT `fk_reg_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON UPDATE CASCADE,
    CONSTRAINT `fk_reg_attendee` FOREIGN KEY (`attendee_id`) REFERENCES `attendees` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: checkins
-- Records when an attendee is checked in at an event
-- ============================================================
CREATE TABLE `checkins` (
    `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `registration_id`   INT UNSIGNED NOT NULL,
    `checked_in_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `checked_in_by`     INT UNSIGNED DEFAULT NULL COMMENT 'Admin who checked in',
    `method`            ENUM('qr','manual','walk_in') NOT NULL DEFAULT 'qr',
    `device_info`       VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_checkin_reg` (`registration_id`),
    KEY `idx_checkin_reg` (`registration_id`),
    CONSTRAINT `fk_checkin_reg` FOREIGN KEY (`registration_id`) REFERENCES `registrations` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_checkin_admin` FOREIGN KEY (`checked_in_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: settings
-- Key-value store for system configuration
-- ============================================================
CREATE TABLE `settings` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `key`           VARCHAR(100) NOT NULL,
    `value`         TEXT DEFAULT NULL,
    `type`          ENUM('text','textarea','boolean','color','email','number','json') NOT NULL DEFAULT 'text',
    `group`         VARCHAR(50) NOT NULL DEFAULT 'general',
    `label`         VARCHAR(150) NOT NULL,
    `updated_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_settings_key` (`key`),
    KEY `idx_settings_group` (`group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: activity_logs
-- Audit trail for admin actions
-- ============================================================
CREATE TABLE `activity_logs` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `admin_id`      INT UNSIGNED DEFAULT NULL,
    `action`        VARCHAR(100) NOT NULL,
    `subject_type`  VARCHAR(50) DEFAULT NULL,
    `subject_id`    INT UNSIGNED DEFAULT NULL,
    `description`   TEXT DEFAULT NULL,
    `ip_address`    VARCHAR(45) DEFAULT NULL,
    `user_agent`    TEXT DEFAULT NULL,
    `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_al_admin` (`admin_id`),
    KEY `idx_al_action` (`action`),
    KEY `idx_al_subject` (`subject_type`, `subject_id`),
    CONSTRAINT `fk_al_admin` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DEFAULT DATA
-- ============================================================

-- Default superadmin (password: Admin@1234)
INSERT INTO `admins` (`name`, `email`, `password`, `role`) VALUES
('System Administrator', 'admin@ems.local', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'superadmin');

-- Default settings
INSERT INTO `settings` (`key`, `value`, `type`, `group`, `label`) VALUES
('church_name',         'My Church',                    'text',     'general',  'Church / Organization Name'),
('church_logo',         '',                             'text',     'general',  'Church Logo URL'),
('primary_color',       '#1a3c5e',                      'color',    'general',  'Primary Color'),
('secondary_color',     '#c8a456',                      'color',    'general',  'Secondary Color'),
('timezone',            'Africa/Lagos',                 'text',     'general',  'Timezone'),
('site_url',            'http://localhost',             'text',     'general',  'Site URL'),
('items_per_page',      '20',                           'number',   'general',  'Records Per Page'),
('smtp_host',           'smtp.mailtrap.io',             'text',     'email',    'SMTP Host'),
('smtp_port',           '587',                          'number',   'email',    'SMTP Port'),
('smtp_user',           '',                             'text',     'email',    'SMTP Username'),
('smtp_pass',           '',                             'text',     'email',    'SMTP Password'),
('smtp_from_email',     'noreply@ems.local',            'email',    'email',    'From Email'),
('smtp_from_name',      'Event Management System',      'text',     'email',    'From Name'),
('smtp_encryption',     'tls',                          'text',     'email',    'Encryption (tls/ssl)'),
('emails_enabled',      '1',                            'boolean',  'email',    'Enable Email Sending'),
('reg_code_prefix',     'EMS',                          'text',     'registration', 'Registration Code Prefix'),
('reg_email_subject',   'Your Registration Confirmation','text',    'registration', 'Confirmation Email Subject');
