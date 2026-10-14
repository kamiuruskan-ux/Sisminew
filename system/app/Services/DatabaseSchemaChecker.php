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
                    $table->string('record_category', 30)->default('ziyadah');
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
                if (!Schema::hasColumn('halaqah_records', 'record_category')) {
                    Schema::table('halaqah_records', function (Blueprint $table) {
                        $table->string('record_category', 30)->default('ziyadah')->after('program_type')->index();
                    });
                    $updatedTables[] = 'halaqah_records (tambah kolom record_category)';
                }

                // Pastikan program_type mendukung 'tilawah' (ubah dari ENUM('tahsin', 'tahfidz') ke VARCHAR)
                try {
                    $colInfo = DB::select("SELECT DATA_TYPE, COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'halaqah_records' AND COLUMN_NAME = 'program_type'");
                    if (!empty($colInfo)) {
                        $colType = strtolower($colInfo[0]->COLUMN_TYPE ?? $colInfo[0]->DATA_TYPE ?? '');
                        if (str_contains($colType, 'enum')) {
                            DB::statement("ALTER TABLE `halaqah_records` MODIFY COLUMN `program_type` VARCHAR(50) NOT NULL DEFAULT 'tahsin'");
                            $updatedTables[] = 'halaqah_records (ubah kolom program_type menjadi varchar)';
                        }
                    }
                } catch (\Throwable $e) {
                    try {
                        DB::statement("ALTER TABLE `halaqah_records` MODIFY COLUMN `program_type` VARCHAR(50) NOT NULL DEFAULT 'tahsin'");
                    } catch (\Throwable $ex) {}
                }
            }

            // 1b. Tabel Target Hafalan & Capaian Al-Qur'an (quran_targets)
            if (!Schema::hasTable('quran_targets')) {
                Schema::create('quran_targets', function (Blueprint $table) {
                    $table->id();
                    $table->integer('grade')->index();
                    $table->unsignedTinyInteger('semester')->default(1);
                    $table->string('program_type', 30)->default('tahfidz'); // tahfidz, tahsin, tilawah
                    $table->string('title', 150);
                    $table->integer('target_juz')->nullable();
                    $table->string('target_surah_start', 100)->nullable();
                    $table->string('target_surah_end', 100)->nullable();
                    $table->string('target_jilid', 50)->nullable();
                    $table->integer('target_page_start')->nullable();
                    $table->integer('target_page_end')->nullable();
                    $table->text('notes')->nullable();
                    $table->timestamps();
                });
                $createdTables[] = 'quran_targets';
            } else {
                if (!Schema::hasColumn('quran_targets', 'target_page_start')) {
                    Schema::table('quran_targets', function (Blueprint $table) {
                        $table->integer('target_page_start')->nullable()->after('target_jilid');
                        $table->integer('target_page_end')->nullable()->after('target_page_start');
                    });
                }
            }

            // 1c. Tabel Ujian Tasmi' & Syahadah Al-Qur'an (quran_tasmi_exams)
            if (!Schema::hasTable('quran_tasmi_exams')) {
                Schema::create('quran_tasmi_exams', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('student_id')->index();
                    $table->unsignedBigInteger('teacher_id')->nullable()->index(); // Musyrif / Penguji
                    $table->unsignedBigInteger('academic_year_id')->nullable();
                    $table->date('exam_date');
                    $table->string('juz_tested', 50)->default('Juz 30');
                    $table->string('surah_range', 150)->nullable();
                    $table->decimal('score_tajwid', 5, 2)->default(0);
                    $table->decimal('score_kelancaran', 5, 2)->default(0);
                    $table->decimal('score_fashohah', 5, 2)->default(0);
                    $table->decimal('score_final', 5, 2)->default(0);
                    $table->string('predicate', 50)->default('Mumtaz');
                    $table->string('status', 30)->default('lulus'); // lulus, perbaikan
                    $table->string('certificate_number', 100)->nullable()->unique();
                    $table->text('notes')->nullable();
                    $table->timestamps();
                });
                $createdTables[] = 'quran_tasmi_exams';
            } else {
                Schema::table('quran_tasmi_exams', function (Blueprint $table) {
                    if (!Schema::hasColumn('quran_tasmi_exams', 'examiner_signature')) {
                        $table->string('examiner_signature', 255)->nullable()->after('notes');
                    }
                    if (!Schema::hasColumn('quran_tasmi_exams', 'principal_signature')) {
                        $table->string('principal_signature', 255)->nullable()->after('notes');
                    }
                });
            }

            // 1d. Tabel Ujian Kenaikan Jilid Tahsin (quran_jilid_exams)
            if (!Schema::hasTable('quran_jilid_exams')) {
                Schema::create('quran_jilid_exams', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('student_id')->index();
                    $table->unsignedBigInteger('teacher_id')->nullable()->index(); // Asatidz Penguji
                    $table->unsignedBigInteger('academic_year_id')->nullable();
                    $table->date('exam_date');
                    $table->string('current_jilid', 50)->default('Jilid 1'); // Jilid yang diuji
                    $table->string('target_jilid', 50)->default('Jilid 2'); // Target kenaikan
                    $table->string('page_tested', 100)->nullable(); // Hal 1 - 40 / Evaluasi Akhir
                    $table->decimal('score_makhraj', 5, 2)->default(0); // Makhorijul & sifat huruf
                    $table->decimal('score_mad', 5, 2)->default(0); // Ketepatan mad & panjang pendek
                    $table->decimal('score_kelancaran', 5, 2)->default(0); // Kelancaran & fashohah
                    $table->decimal('score_final', 5, 2)->default(0);
                    $table->string('predicate', 50)->default('Mumtaz');
                    $table->string('status', 30)->default('lulus'); // lulus, perbaikan
                    $table->string('certificate_number', 100)->nullable()->unique();
                    $table->text('notes')->nullable();
                    $table->timestamps();

                    $table->index(['student_id', 'exam_date']);
                    $table->index(['current_jilid', 'status']);
                });
                $createdTables[] = 'quran_jilid_exams';
            } else {
                Schema::table('quran_jilid_exams', function (Blueprint $table) {
                    if (!Schema::hasColumn('quran_jilid_exams', 'examiner_signature')) {
                        $table->string('examiner_signature', 255)->nullable()->after('notes');
                    }
                    if (!Schema::hasColumn('quran_jilid_exams', 'principal_signature')) {
                        $table->string('principal_signature', 255)->nullable()->after('notes');
                    }
                });
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

            // 15. Tabel Riwayat Kenaikan Kelas Siswa per Tahun Ajaran (student_class_histories)
            if (!Schema::hasTable('student_class_histories')) {
                Schema::create('student_class_histories', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('student_id');
                    $table->unsignedBigInteger('class_id')->nullable();
                    $table->unsignedBigInteger('academic_year_id');
                    $table->string('status', 30)->default('active'); // active, promoted, retained, graduated, transferred
                    $table->text('notes')->nullable();
                    $table->timestamp('promoted_at')->nullable();
                    $table->unsignedBigInteger('promoted_by')->nullable();
                    $table->timestamps();

                    $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
                    $table->index(['student_id', 'academic_year_id'], 'idx_sch_std_year');
                    $table->index(['class_id', 'academic_year_id'], 'idx_sch_cls_year');
                    $table->index('status', 'idx_sch_status');
                });
                $createdTables[] = 'student_class_histories';
            }

            // Auto-backfill riwayat kelas awal jika tabel student_class_histories masih kosong
            try {
                $historyCount = \DB::table('student_class_histories')->count();
                $activeYear = \App\Models\AcademicYear::where('is_active', true)->first() ?? \App\Models\AcademicYear::first();
                if ($historyCount === 0 && $activeYear) {
                    $activeStudents = \App\Models\Student::whereNotNull('class_id')->get(['id', 'class_id']);
                    $historiesToInsert = [];
                    $now = now();
                    foreach ($activeStudents as $st) {
                        $historiesToInsert[] = [
                            'student_id' => $st->id,
                            'class_id' => $st->class_id,
                            'academic_year_id' => $activeYear->id,
                            'status' => 'active',
                            'notes' => 'Riwayat kelas awal sistem',
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                    if (!empty($historiesToInsert)) {
                        foreach (array_chunk($historiesToInsert, 200) as $chunk) {
                            \DB::table('student_class_histories')->insert($chunk);
                        }
                        $updatedTables[] = 'student_class_histories (auto-backfill ' . count($historiesToInsert) . ' siswa)';
                    }
                }
            } catch (\Throwable $e) {}

            // 16. Kolom Tahun Ajaran & Semester pada Tabel Nilai (grades)
            if (Schema::hasTable('grades')) {
                $hasGradYr = Schema::hasColumn('grades', 'academic_year_id');
                $hasGradSem = Schema::hasColumn('grades', 'semester');
                if (!$hasGradYr || !$hasGradSem) {
                    Schema::table('grades', function (Blueprint $table) use ($hasGradYr, $hasGradSem) {
                        if (!$hasGradYr) {
                            $table->unsignedBigInteger('academic_year_id')->nullable()->after('class_id')->index();
                        }
                        if (!$hasGradSem) {
                            $table->string('semester', 20)->default('ganjil')->after('academic_year_id')->index();
                        }
                    });
                    $updatedTables[] = 'grades (tambah academic_year_id & semester)';
                }

                // Backfill grades yang belum memiliki academic_year_id
                try {
                    $activeYear = \App\Models\AcademicYear::where('is_active', true)->first() ?? \App\Models\AcademicYear::first();
                    if ($activeYear) {
                        \DB::table('grades')->whereNull('academic_year_id')->update([
                            'academic_year_id' => $activeYear->id,
                            'semester' => $activeYear->semester ?? 'ganjil',
                        ]);
                    }
                } catch (\Throwable $e) {}
            }

            // 17. Kolom Tahun Ajaran pada Tabel Kalender & Absen Kegiatan (activity_events & activity_attendances)
            if (Schema::hasTable('activity_events')) {
                if (!Schema::hasColumn('activity_events', 'academic_year_id')) {
                    Schema::table('activity_events', function (Blueprint $table) {
                        $table->unsignedBigInteger('academic_year_id')->nullable()->after('class_id')->index();
                    });
                    $updatedTables[] = 'activity_events (tambah academic_year_id)';
                }
            }
            if (Schema::hasTable('activity_attendances')) {
                $hasActYr = Schema::hasColumn('activity_attendances', 'academic_year_id');
                $hasActCls = Schema::hasColumn('activity_attendances', 'class_id');
                if (!$hasActYr || !$hasActCls) {
                    Schema::table('activity_attendances', function (Blueprint $table) use ($hasActYr, $hasActCls) {
                        if (!$hasActYr) {
                            $table->unsignedBigInteger('academic_year_id')->nullable()->after('event_id')->index();
                        }
                        if (!$hasActCls) {
                            $table->unsignedBigInteger('class_id')->nullable()->after('student_id')->index();
                        }
                    });
                    $updatedTables[] = 'activity_attendances (tambah academic_year_id & class_id)';
                }
            }

            // 18. Kolom Tahun Ajaran pada KPI & Presensi Guru (kpi_evaluations & teacher_attendances)
            if (Schema::hasTable('kpi_evaluations')) {
                if (!Schema::hasColumn('kpi_evaluations', 'academic_year_id')) {
                    Schema::table('kpi_evaluations', function (Blueprint $table) {
                        $table->unsignedBigInteger('academic_year_id')->nullable()->after('period_semester')->index();
                    });
                    $updatedTables[] = 'kpi_evaluations (tambah academic_year_id)';
                }
            }
            if (Schema::hasTable('teacher_attendances')) {
                if (!Schema::hasColumn('teacher_attendances', 'academic_year_id')) {
                    Schema::table('teacher_attendances', function (Blueprint $table) {
                        $table->unsignedBigInteger('academic_year_id')->nullable()->after('date')->index();
                    });
                    $updatedTables[] = 'teacher_attendances (tambah academic_year_id)';
                }
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
