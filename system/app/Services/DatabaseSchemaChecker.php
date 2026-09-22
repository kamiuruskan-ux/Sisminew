<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\BriefingSession;
use App\Models\CustomNotification;
use App\Models\EmployeeMutabaah;
use App\Models\EmployeeStudySession;
use App\Models\KpiEvaluation;
use App\Models\PushSubscription;
use App\Models\SchoolFeedback;
use App\Models\TeachingAgenda;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DatabaseSchemaChecker
{
    /**
     * Pastikan seluruh tabel dan kolom untuk fitur-fitur baru (Presensi Guru, KPI,
     * Penilaian Quran, Jurnal Guru, dsb.) sudah tersedia di database MySQL.
     * Dapat dipanggil otomatis tanpa perlu akses command-line terminal.
     */
    public static function ensureAllNewTablesExist(): array
    {
        $createdTables = [];
        $updatedTables = [];

        try {
            // 1. Tabel halaqah_records & kolom grade
            if (!Schema::hasTable('halaqah_records')) {
                Schema::create('halaqah_records', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('student_id');
                    $table->unsignedBigInteger('teacher_id')->nullable();
                    $table->unsignedBigInteger('class_id')->nullable();
                    $table->integer('grade')->nullable()->index();
                    $table->unsignedBigInteger('academic_year_id')->nullable();
                    $table->date('assessment_date');
                    $table->string('attendance_status', 20)->default('hadir');
                    $table->string('program_type', 20)->default('tahsin');
                    $table->string('tahsin_type', 50)->nullable()->default('jilid');
                    $table->string('jilid_level', 50)->nullable();
                    $table->integer('page_start')->nullable();
                    $table->integer('page_end')->nullable();
                    $table->string('surah_name', 100)->nullable();
                    $table->integer('ayat_start')->nullable();
                    $table->integer('ayat_end')->nullable();
                    $table->integer('juz_number')->nullable()->default(30);
                    $table->decimal('score_cognitive', 5, 2)->default(0);
                    $table->decimal('score_adab', 5, 2)->default(0);
                    $table->string('predicate', 50)->default('Mumtaz');
                    $table->text('teacher_notes')->nullable();
                    $table->timestamps();

                    $table->index(['assessment_date', 'program_type']);
                    $table->index(['student_id', 'assessment_date']);
                    $table->index(['class_id', 'assessment_date']);
                    $table->index(['teacher_id', 'assessment_date']);
                });
                $createdTables[] = 'halaqah_records';
            } else {
                if (!Schema::hasColumn('halaqah_records', 'grade')) {
                    Schema::table('halaqah_records', function (Blueprint $table) {
                        $table->integer('grade')->nullable()->after('academic_year_id')->index();
                    });
                    $updatedTables[] = 'halaqah_records (tambah kolom grade)';
                }
            }

            // 2. Tabel quran_halaqah_members
            if (!Schema::hasTable('quran_halaqah_members')) {
                Schema::create('quran_halaqah_members', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('teacher_id');
                    $table->unsignedBigInteger('student_id');
                    $table->unsignedBigInteger('academic_year_id')->nullable();
                    $table->integer('grade')->nullable();
                    $table->string('group_name', 100)->nullable();
                    $table->timestamps();

                    $table->unique('student_id');
                    $table->index(['teacher_id', 'grade']);
                    $table->index('grade');
                });
                $createdTables[] = 'quran_halaqah_members';
            }

            // 3. Tabel teacher_attendances (Presensi Guru)
            if (!Schema::hasTable('teacher_attendances')) {
                Schema::create('teacher_attendances', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('user_id');
                    $table->date('date');
                    $table->time('check_in')->nullable();
                    $table->time('midday_at')->nullable();
                    $table->time('check_out')->nullable();
                    $table->string('status', 30)->default('present');
                    $table->integer('delay_minutes')->default(0);
                    $table->string('session_name', 50)->nullable();
                    $table->string('verification_status', 30)->default('verified');
                    $table->string('work_location', 20)->default('school');
                    $table->string('check_in_lat')->nullable();
                    $table->string('check_in_long')->nullable();
                    $table->string('check_out_lat')->nullable();
                    $table->string('check_out_long')->nullable();
                    $table->string('check_in_photo')->nullable();
                    $table->string('check_out_photo')->nullable();
                    $table->string('method', 50)->default('manual');
                    $table->text('device_info')->nullable();
                    $table->text('notes')->nullable();
                    $table->string('attachment')->nullable();
                    $table->unsignedBigInteger('recorded_by')->nullable();
                    $table->timestamps();

                    $table->unique(['user_id', 'date']);
                    $table->index(['date', 'status']);
                });
                $createdTables[] = 'teacher_attendances';
            } else {
                Schema::table('teacher_attendances', function (Blueprint $table) {
                    if (!Schema::hasColumn('teacher_attendances', 'midday_at')) {
                        $table->time('midday_at')->nullable()->after('check_in');
                    }
                    if (!Schema::hasColumn('teacher_attendances', 'delay_minutes')) {
                        $table->integer('delay_minutes')->default(0)->after('status');
                    }
                    if (!Schema::hasColumn('teacher_attendances', 'session_name')) {
                        $table->string('session_name', 50)->nullable()->after('status');
                    }
                    if (!Schema::hasColumn('teacher_attendances', 'verification_status')) {
                        $table->string('verification_status', 30)->default('verified')->after('session_name');
                    }
                    if (!Schema::hasColumn('teacher_attendances', 'method')) {
                        $table->string('method', 50)->default('manual')->after('check_out_photo');
                    }
                    if (!Schema::hasColumn('teacher_attendances', 'device_info')) {
                        $table->text('device_info')->nullable()->after('method');
                    }
                });
            }

            // 4. Tabel attendance_audit_logs
            if (!Schema::hasTable('attendance_audit_logs')) {
                Schema::create('attendance_audit_logs', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('user_id');
                    $table->string('action', 50);
                    $table->string('ip_address', 45)->nullable();
                    $table->text('user_agent')->nullable();
                    $table->json('details')->nullable();
                    $table->timestamps();

                    $table->index('user_id');
                });
                $createdTables[] = 'attendance_audit_logs';
            }

            // 5. Tabel employee_permits (Izin Pegawai)
            if (!Schema::hasTable('employee_permits')) {
                Schema::create('employee_permits', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('user_id');
                    $table->string('permit_type', 30);
                    $table->date('start_date');
                    $table->date('end_date');
                    $table->text('reason');
                    $table->string('attachment')->nullable();
                    $table->string('status', 30)->default('pending');
                    $table->unsignedBigInteger('approved_by')->nullable();
                    $table->timestamp('approved_at')->nullable();
                    $table->text('approval_notes')->nullable();
                    $table->timestamps();

                    $table->index(['user_id', 'status']);
                });
                $createdTables[] = 'employee_permits';
            }

            // 6. Tabel employee_tasks (Checklist / Tugas Pegawai)
            if (!Schema::hasTable('employee_tasks')) {
                Schema::create('employee_tasks', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('user_id');
                    $table->unsignedBigInteger('assigned_by')->nullable();
                    $table->string('title', 255);
                    $table->text('description')->nullable();
                    $table->string('priority', 20)->default('medium');
                    $table->string('status', 20)->default('pending');
                    $table->date('due_date')->nullable();
                    $table->timestamp('completed_at')->nullable();
                    $table->text('notes')->nullable();
                    $table->timestamps();

                    $table->index(['user_id', 'status']);
                });
                $createdTables[] = 'employee_tasks';
            }

            // 7. Tabel Jurnal Guru (TeachingAgenda & TeachingAgendaStudents)
            TeachingAgenda::ensureTableExists();

            // 8. Tabel KPI (KpiEvaluation & KpiEvaluationItems)
            KpiEvaluation::ensureTablesExist();

            // 9. Tabel Mutabaah Pegawai (EmployeeMutabaah)
            EmployeeMutabaah::ensureTablesExist();

            // 10. Tabel Briefing (BriefingSession & BriefingAttendance)
            BriefingSession::ensureTablesExist();

            // 11. Tabel Kajian Pegawai (EmployeeStudySession & EmployeeStudyAttendance)
            EmployeeStudySession::ensureTablesExist();

            // 12. Tabel Saran & Feedback (SchoolFeedback)
            SchoolFeedback::ensureTableExists();

            // 13. Tabel Push Subscriptions & Notifikasi Custom (PushNotification, CustomNotification, AppNotification)
            PushSubscription::ensureTableExists();
            CustomNotification::ensureTableExists();
            AppNotification::ensureTableExists();

            // 13. Kolom pelengkap di tabel users jika belum ada
            if (Schema::hasTable('users')) {
                Schema::table('users', function (Blueprint $table) {
                    if (!Schema::hasColumn('users', 'nip')) {
                        $table->string('nip', 50)->nullable()->after('email');
                    }
                    if (!Schema::hasColumn('users', 'jabatan')) {
                        $table->string('jabatan', 100)->nullable()->after('name');
                    }
                    if (!Schema::hasColumn('users', 'face_id')) {
                        $table->string('face_id', 255)->nullable();
                    }
                    if (!Schema::hasColumn('users', 'tmt_date')) {
                        $table->date('tmt_date')->nullable();
                    }
                    if (!Schema::hasColumn('users', 'education_level')) {
                        $table->string('education_level', 50)->nullable();
                    }
                });
            }

            // 14. Kolom grade di tabel classes jika belum ada
            if (Schema::hasTable('classes')) {
                Schema::table('classes', function (Blueprint $table) {
                    if (!Schema::hasColumn('classes', 'grade')) {
                        $table->integer('grade')->nullable()->after('name')->index();
                    }
                });
            }

        } catch (\Throwable $e) {
            // Tangkap exception tanpa menghentikan eksekusi aplikasi
        }

        return [
            'created' => $createdTables,
            'updated' => $updatedTables,
        ];
    }
}
