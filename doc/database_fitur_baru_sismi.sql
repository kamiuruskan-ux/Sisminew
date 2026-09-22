-- ==============================================================================
-- SISMI WEB - SKRIP TABEL DATABASE FITUR TERBARU (MySQL / MariaDB)
-- File: doc/database_fitur_baru_sismi.sql
-- Fitur: Presensi Guru & Karyawan, KPI & Mutabaah, Penilaian Al-Qur'an (Halaqah),
--        Jurnal Guru (Agenda Mengajar), Briefing, Kajian Pegawai, dan Feedback.
--
-- Petunjuk Import via phpMyAdmin (cPanel / Laragon / XAMPP):
-- 1. Buka phpMyAdmin
-- 2. Pilih nama database Anda (contoh: sekolah_lrv atau nama database cPanel)
-- 3. Klik tab 'Import' di menu atas
-- 4. Pilih file ini (database_fitur_baru_sismi.sql) lalu klik 'Kirim' / 'Go'
-- ==============================================================================

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+07:00";

-- 1. Tabel Penilaian Al-Qur'an (Tahsin & Tahfidz Halaqah)
CREATE TABLE IF NOT EXISTS `halaqah_records` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `teacher_id` bigint(20) unsigned DEFAULT NULL,
  `class_id` bigint(20) unsigned DEFAULT NULL,
  `grade` int(11) DEFAULT NULL,
  `academic_year_id` bigint(20) unsigned DEFAULT NULL,
  `assessment_date` date NOT NULL,
  `attendance_status` varchar(20) NOT NULL DEFAULT 'hadir',
  `program_type` varchar(20) NOT NULL DEFAULT 'tahsin',
  `tahsin_type` varchar(50) DEFAULT 'jilid',
  `jilid_level` varchar(50) DEFAULT NULL,
  `page_start` int(11) DEFAULT NULL,
  `page_end` int(11) DEFAULT NULL,
  `surah_name` varchar(100) DEFAULT NULL,
  `ayat_start` int(11) DEFAULT NULL,
  `ayat_end` int(11) DEFAULT NULL,
  `juz_number` int(11) DEFAULT 30,
  `score_cognitive` decimal(5,2) NOT NULL DEFAULT 0.00,
  `score_adab` decimal(5,2) NOT NULL DEFAULT 0.00,
  `predicate` varchar(50) NOT NULL DEFAULT 'Mumtaz',
  `teacher_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `halaqah_records_assessment_date_program_type_index` (`assessment_date`,`program_type`),
  KEY `halaqah_records_student_id_assessment_date_index` (`student_id`,`assessment_date`),
  KEY `halaqah_records_class_id_assessment_date_index` (`class_id`,`assessment_date`),
  KEY `halaqah_records_teacher_id_index` (`teacher_id`),
  KEY `halaqah_records_grade_index` (`grade`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tabel Anggota Kelompok Halaqah Al-Qur'an
CREATE TABLE IF NOT EXISTS `quran_halaqah_members` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `teacher_id` bigint(20) unsigned NOT NULL,
  `student_id` bigint(20) unsigned NOT NULL,
  `academic_year_id` bigint(20) unsigned DEFAULT NULL,
  `grade` int(11) DEFAULT NULL,
  `group_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `quran_halaqah_members_student_id_unique` (`student_id`),
  KEY `quran_halaqah_members_teacher_id_grade_index` (`teacher_id`,`grade`),
  KEY `quran_halaqah_members_grade_index` (`grade`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Tabel Presensi Guru & Pegawai
CREATE TABLE IF NOT EXISTS `teacher_attendances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `date` date NOT NULL,
  `check_in` time DEFAULT NULL,
  `midday_at` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `status` enum('present','late','very_late','outside_window','sick','permission','absent') NOT NULL DEFAULT 'present',
  `delay_minutes` int(11) DEFAULT 0,
  `session_name` varchar(50) DEFAULT NULL,
  `verification_status` varchar(30) NOT NULL DEFAULT 'verified',
  `work_location` enum('school','home','outstation') NOT NULL DEFAULT 'school',
  `check_in_lat` varchar(255) DEFAULT NULL,
  `check_in_long` varchar(255) DEFAULT NULL,
  `check_out_lat` varchar(255) DEFAULT NULL,
  `check_out_long` varchar(255) DEFAULT NULL,
  `check_in_photo` varchar(255) DEFAULT NULL,
  `check_out_photo` varchar(255) DEFAULT NULL,
  `method` varchar(50) DEFAULT 'manual',
  `device_info` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `recorded_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `teacher_attendances_user_id_date_unique` (`user_id`,`date`),
  KEY `teacher_attendances_date_status_index` (`date`,`status`),
  KEY `teacher_attendances_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Tabel Audit Log Presensi Pegawai
CREATE TABLE IF NOT EXISTS `attendance_audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `action` varchar(50) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `details` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendance_audit_logs_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Tabel Pengajuan Izin Guru & Pegawai
CREATE TABLE IF NOT EXISTS `employee_permits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `permit_type` enum('sick','permission','leave','duty') NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approval_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_permits_user_id_status_index` (`user_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Tabel Tugas & Checklist Harian Pegawai
CREATE TABLE IF NOT EXISTS `employee_tasks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `assigned_by` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `status` enum('pending','in_progress','completed','cancelled') NOT NULL DEFAULT 'pending',
  `due_date` date DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_tasks_user_id_status_index` (`user_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Tabel Jurnal Guru (Agenda Mengajar)
CREATE TABLE IF NOT EXISTS `teaching_agendas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `teacher_id` bigint(20) unsigned NOT NULL,
  `class_id` bigint(20) unsigned NOT NULL,
  `academic_year_id` bigint(20) unsigned DEFAULT NULL,
  `subject` varchar(150) NOT NULL,
  `date` date NOT NULL,
  `material_taught` text NOT NULL,
  `class_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `teaching_agendas_teacher_id_date_index` (`teacher_id`,`date`),
  KEY `teaching_agendas_class_id_subject_index` (`class_id`,`subject`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `teaching_agenda_students` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `teaching_agenda_id` bigint(20) unsigned NOT NULL,
  `student_id` bigint(20) unsigned NOT NULL,
  `attendance_status` varchar(20) NOT NULL DEFAULT 'hadir',
  `score_cognitive` decimal(5,2) NOT NULL DEFAULT 80.00,
  `score_adab` decimal(5,2) NOT NULL DEFAULT 80.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `teaching_agenda_students_agenda_index` (`teaching_agenda_id`),
  KEY `teaching_agenda_students_student_id_index` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Tabel Evaluasi KPI Pegawai & Guru
CREATE TABLE IF NOT EXISTS `kpi_evaluations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `evaluator_id` bigint(20) unsigned DEFAULT NULL,
  `period_type` varchar(20) NOT NULL DEFAULT 'month',
  `period_year` smallint(5) unsigned NOT NULL,
  `period_month` tinyint(3) unsigned DEFAULT NULL,
  `period_semester` tinyint(3) unsigned DEFAULT NULL,
  `score_comp_1` decimal(5,2) NOT NULL DEFAULT 0.00,
  `score_comp_2` decimal(5,2) NOT NULL DEFAULT 0.00,
  `score_comp_3` decimal(5,2) NOT NULL DEFAULT 0.00,
  `score_comp_4` decimal(5,2) NOT NULL DEFAULT 0.00,
  `score_comp_5` decimal(5,2) NOT NULL DEFAULT 0.00,
  `final_score` decimal(5,2) NOT NULL DEFAULT 0.00,
  `predicate` varchar(10) NOT NULL DEFAULT 'C',
  `attendance_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `gate_passed` tinyint(1) NOT NULL DEFAULT 1,
  `is_capped` tinyint(1) NOT NULL DEFAULT 0,
  `feedback_appreciation` text DEFAULT NULL,
  `feedback_improvement` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kpi_user_period_unique` (`user_id`,`period_year`,`period_month`,`period_type`),
  KEY `kpi_evaluations_period_index` (`period_year`,`period_month`),
  KEY `kpi_evaluations_score_index` (`predicate`,`final_score`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `kpi_evaluation_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kpi_evaluation_id` bigint(20) unsigned NOT NULL,
  `indicator_code` varchar(30) NOT NULL,
  `score` decimal(5,2) NOT NULL DEFAULT 0.00,
  `source_type` varchar(20) NOT NULL DEFAULT 'auto',
  `calc_data` json DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kpi_items_evaluation_id_index` (`kpi_evaluation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Tabel Mutabaah Ibadah Yaumiyah Pegawai
CREATE TABLE IF NOT EXISTS `employee_mutabaahs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `date` date NOT NULL,
  `subuh_jamaah` tinyint(1) NOT NULL DEFAULT 0,
  `dzuhur_jamaah` tinyint(1) NOT NULL DEFAULT 0,
  `ashar_jamaah` tinyint(1) NOT NULL DEFAULT 0,
  `maghrib_jamaah` tinyint(1) NOT NULL DEFAULT 0,
  `isya_jamaah` tinyint(1) NOT NULL DEFAULT 0,
  `rawatib_count` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `dhuha` tinyint(1) NOT NULL DEFAULT 0,
  `tahajjud_witir` tinyint(1) NOT NULL DEFAULT 0,
  `tilawah_pages` smallint(5) unsigned NOT NULL DEFAULT 0,
  `dzikir_pagi_petang` tinyint(1) NOT NULL DEFAULT 0,
  `puasa_sunnah` tinyint(1) NOT NULL DEFAULT 0,
  `sedekah` tinyint(1) NOT NULL DEFAULT 0,
  `daily_score` decimal(5,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_mutabaah_unique` (`user_id`,`date`),
  KEY `employee_mutabaahs_date_score_index` (`date`,`daily_score`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Tabel Briefing Pegawai
CREATE TABLE IF NOT EXISTS `briefing_sessions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `location` varchar(150) DEFAULT 'Ruang Guru / Aula',
  `leader_id` bigint(20) unsigned DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'open',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `briefing_attendances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `briefing_session_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `status` enum('present','late','sick','permission','absent') NOT NULL DEFAULT 'present',
  `check_in_time` time DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `briefing_session_user_unique` (`briefing_session_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Tabel Kajian / Pembinaan Rohani Pegawai
CREATE TABLE IF NOT EXISTS `employee_study_sessions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `speaker` varchar(150) DEFAULT NULL,
  `date` date NOT NULL,
  `location` varchar(150) DEFAULT 'Musholla / Aula',
  `material_summary` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `employee_study_attendances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `status` enum('present','permission','sick','absent') NOT NULL DEFAULT 'present',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_study_session_user_unique` (`session_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Tabel Saran & Masukan Pegawai (School Feedback)
CREATE TABLE IF NOT EXISTS `school_feedbacks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'saran',
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `is_anonymous` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `admin_response` text DEFAULT NULL,
  `responded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Tabel Push Subscriptions (Web Push HP & PC)
CREATE TABLE IF NOT EXISTS `push_subscriptions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `endpoint` varchar(500) NOT NULL,
  `public_key` text DEFAULT NULL,
  `auth_token` varchar(255) DEFAULT NULL,
  `content_encoding` varchar(50) NOT NULL DEFAULT 'aes128gcm',
  `device_type` varchar(50) DEFAULT 'desktop',
  `user_agent` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_active_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `push_subscriptions_endpoint_unique` (`endpoint`),
  KEY `push_subscriptions_user_active_index` (`user_id`,`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. Tabel Siaran Notifikasi Custom (Custom Notifications)
CREATE TABLE IF NOT EXISTS `custom_notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'info',
  `target_type` varchar(50) NOT NULL DEFAULT 'all',
  `target_payload` json DEFAULT NULL,
  `action_url` varchar(255) DEFAULT NULL,
  `channels` json DEFAULT NULL,
  `sent_count` int(11) NOT NULL DEFAULT 0,
  `read_count` int(11) NOT NULL DEFAULT 0,
  `status` varchar(30) NOT NULL DEFAULT 'sent',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `custom_notifications_type_index` (`type`),
  KEY `custom_notifications_target_type_index` (`target_type`),
  KEY `custom_notifications_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. Tabel Riwayat Notifikasi per Penerima (App Notifications)
CREATE TABLE IF NOT EXISTS `app_notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `custom_notification_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'info',
  `category` varchar(50) NOT NULL DEFAULT 'general',
  `action_url` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `is_pushed` tinyint(1) NOT NULL DEFAULT 0,
  `pushed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `app_notifications_user_read_index` (`user_id`,`is_read`,`created_at`),
  KEY `app_notifications_custom_id_index` (`custom_notification_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS=1;
COMMIT;

