<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // =========================================================
        // ROLES
        // =========================================================
        $superAdmin = Role::firstOrCreate(['slug' => 'super-admin'], [
            'name'        => 'Super Admin',
            'description' => 'Full system access — no restrictions',
            'is_active'   => true,
        ]);

        $admin = Role::firstOrCreate(['slug' => 'admin'], [
            'name'        => 'Admin',
            'description' => 'School administrator — all features except role management',
            'is_active'   => true,
        ]);

        $guru = Role::firstOrCreate(['slug' => 'guru'], [
            'name'        => 'Guru',
            'description' => 'Teacher — learning, attendance, grading, announcements',
            'is_active'   => true,
        ]);

        $bendahara = Role::firstOrCreate(['slug' => 'bendahara'], [
            'name'        => 'Bendahara',
            'description' => 'Finance staff — full financial access, student & SPMB view',
            'is_active'   => true,
        ]);

        $operator = Role::firstOrCreate(['slug' => 'operator'], [
            'name'        => 'Operator',
            'description' => 'TU / Data operator — student data entry, SPMB, academic structure',
            'is_active'   => true,
        ]);

        $staff = Role::firstOrCreate(['slug' => 'staff'], [
            'name'        => 'Staff',
            'description' => 'General staff — limited dashboard, announcements, notifications',
            'is_active'   => true,
        ]);

        $guruBk = Role::firstOrCreate(['slug' => 'guru-bk'], [
            'name'        => 'Guru BK',
            'description' => 'Bimbingan & Konseling — counseling services, student violations, assessments',
            'is_active'   => true,
        ]);

        $student = Role::firstOrCreate(['slug' => 'student'], [
            'name'        => 'Siswa',
            'description' => 'Student portal access only',
            'is_active'   => true,
        ]);

        $calonSiswa = Role::firstOrCreate(['slug' => 'calon-siswa'], [
            'name'        => 'Calon Siswa',
            'description' => 'Prospective student — SPMB registration portal only',
            'is_active'   => true,
        ]);

        $kantin = Role::firstOrCreate(['slug' => 'kantin'], [
            'name'        => 'Kantin / Vendor',
            'description' => 'Role Pengelola Vendor Kantin Sekolah',
            'is_active'   => true,
        ]);

        // =========================================================
        // PERMISSIONS
        // =========================================================
        $permissions = [
            // --- Dashboard ---
            ['name' => 'View Dashboard Admin',       'slug' => 'view-dashboard-admin',       'group' => 'dashboard'],
            ['name' => 'View Dashboard Bendahara',   'slug' => 'view-dashboard-bendahara',   'group' => 'dashboard'],
            ['name' => 'View Dashboard Guru',        'slug' => 'view-dashboard-guru',        'group' => 'dashboard'],
            ['name' => 'View Dashboard BK',          'slug' => 'view-dashboard-bk',          'group' => 'dashboard'],
            ['name' => 'View Dashboard Operator TU', 'slug' => 'view-dashboard-operator',    'group' => 'dashboard'],
            ['name' => 'View Dashboard Staff',       'slug' => 'view-dashboard-staff',       'group' => 'dashboard'],

            // --- User Management (Super Admin only) ---
            ['name' => 'View Users',             'slug' => 'view-users',             'group' => 'users'],
            ['name' => 'Create Users',           'slug' => 'create-users',           'group' => 'users'],
            ['name' => 'Edit Users',             'slug' => 'edit-users',             'group' => 'users'],
            ['name' => 'Delete Users',           'slug' => 'delete-users',           'group' => 'users'],

            // --- Role Management (Super Admin only) ---
            ['name' => 'View Roles',             'slug' => 'view-roles',             'group' => 'roles'],
            ['name' => 'Create Roles',           'slug' => 'create-roles',           'group' => 'roles'],
            ['name' => 'Edit Roles',             'slug' => 'edit-roles',             'group' => 'roles'],
            ['name' => 'Delete Roles',           'slug' => 'delete-roles',           'group' => 'roles'],

            // --- Student Management ---
            ['name' => 'View Students',          'slug' => 'view-students',          'group' => 'students'],
            ['name' => 'Create Students',        'slug' => 'create-students',        'group' => 'students'],
            ['name' => 'Edit Students',          'slug' => 'edit-students',          'group' => 'students'],
            ['name' => 'Delete Students',        'slug' => 'delete-students',        'group' => 'students'],

            // --- Class Management ---
            ['name' => 'View Classes',           'slug' => 'view-classes',           'group' => 'classes'],
            ['name' => 'Create Classes',         'slug' => 'create-classes',         'group' => 'classes'],
            ['name' => 'Edit Classes',           'slug' => 'edit-classes',           'group' => 'classes'],
            ['name' => 'Delete Classes',         'slug' => 'delete-classes',         'group' => 'classes'],

            // --- Major Management ---
            ['name' => 'View Majors',            'slug' => 'view-majors',            'group' => 'majors'],
            ['name' => 'Create Majors',          'slug' => 'create-majors',          'group' => 'majors'],
            ['name' => 'Edit Majors',            'slug' => 'edit-majors',            'group' => 'majors'],
            ['name' => 'Delete Majors',          'slug' => 'delete-majors',          'group' => 'majors'],

            // --- Academic Year Management ---
            ['name' => 'View Academic Years',    'slug' => 'view-academic-years',    'group' => 'academic-years'],
            ['name' => 'Create Academic Years',  'slug' => 'create-academic-years',  'group' => 'academic-years'],
            ['name' => 'Edit Academic Years',    'slug' => 'edit-academic-years',    'group' => 'academic-years'],
            ['name' => 'Delete Academic Years',  'slug' => 'delete-academic-years',  'group' => 'academic-years'],

            // --- SPMB Management ---
            ['name' => 'View SPMB',              'slug' => 'view-spmb',              'group' => 'spmb'],
            ['name' => 'Create SPMB',            'slug' => 'create-spmb',            'group' => 'spmb'],
            ['name' => 'Edit SPMB',              'slug' => 'edit-spmb',              'group' => 'spmb'],
            ['name' => 'Delete SPMB',            'slug' => 'delete-spmb',            'group' => 'spmb'],
            ['name' => 'Verify SPMB',            'slug' => 'verify-spmb',            'group' => 'spmb'],

            // --- Learning / CBT (Guru) ---
            ['name' => 'View Learning',          'slug' => 'view-learning',          'group' => 'learning'],
            ['name' => 'Manage Learning',        'slug' => 'manage-learning',        'group' => 'learning'],

            // --- Attendance (Guru + Operator) ---
            ['name' => 'View Attendance',        'slug' => 'view-attendance',        'group' => 'attendance'],
            ['name' => 'Manage Attendance',      'slug' => 'manage-attendance',      'group' => 'attendance'],

            // --- Financial / Keuangan (Bendahara) ---
            ['name' => 'View Financial',         'slug' => 'view-financial',         'group' => 'financial'],
            ['name' => 'Manage Financial',       'slug' => 'manage-financial',       'group' => 'financial'],

            // --- Canteen Admin (E-Kantin Admin) ---
            ['name' => 'View Canteen Admin',     'slug' => 'view-canteen-admin',     'group' => 'canteen-admin'],
            ['name' => 'Manage Canteen Admin',   'slug' => 'manage-canteen-admin',   'group' => 'canteen-admin'],

            // --- Bimbingan & Konseling (BK) ---
            ['name' => 'View BK',                'slug' => 'view-bk',                'group' => 'bk'],
            ['name' => 'Manage BK',              'slug' => 'manage-bk',              'group' => 'bk'],

            // --- Content / Konten Website ---
            ['name' => 'View Content',           'slug' => 'view-content',           'group' => 'content'],
            ['name' => 'Manage Content',         'slug' => 'manage-content',         'group' => 'content'],

            // --- Broadcast WhatsApp ---
            ['name' => 'View Broadcast',         'slug' => 'view-broadcast',         'group' => 'broadcast'],

            // --- Notifications ---
            ['name' => 'View Notifications',     'slug' => 'view-notifications',     'group' => 'notifications'],

            // --- Announcements (Guru & Staff dapat buat) ---
            ['name' => 'View Announcements',     'slug' => 'view-announcements',     'group' => 'announcements'],
            ['name' => 'Create Announcements',   'slug' => 'create-announcements',   'group' => 'announcements'],
            ['name' => 'Edit Announcements',     'slug' => 'edit-announcements',     'group' => 'announcements'],
            ['name' => 'Delete Announcements',   'slug' => 'delete-announcements',   'group' => 'announcements'],

            // --- Gallery ---
            ['name' => 'View Gallery',           'slug' => 'view-gallery',           'group' => 'gallery'],
            ['name' => 'Create Gallery',         'slug' => 'create-gallery',         'group' => 'gallery'],
            ['name' => 'Edit Gallery',           'slug' => 'edit-gallery',           'group' => 'gallery'],
            ['name' => 'Delete Gallery',         'slug' => 'delete-gallery',         'group' => 'gallery'],

            // --- Posts / Berita ---
            ['name' => 'View Posts',             'slug' => 'view-posts',             'group' => 'posts'],
            ['name' => 'Create Posts',           'slug' => 'create-posts',           'group' => 'posts'],
            ['name' => 'Edit Posts',             'slug' => 'edit-posts',             'group' => 'posts'],
            ['name' => 'Delete Posts',           'slug' => 'delete-posts',           'group' => 'posts'],

            // --- Categories / Tags ---
            ['name' => 'View Categories',        'slug' => 'view-categories',        'group' => 'categories'],
            ['name' => 'Create Categories',      'slug' => 'create-categories',      'group' => 'categories'],
            ['name' => 'Edit Categories',        'slug' => 'edit-categories',        'group' => 'categories'],
            ['name' => 'Delete Categories',      'slug' => 'delete-categories',      'group' => 'categories'],
            ['name' => 'View Tags',              'slug' => 'view-tags',              'group' => 'tags'],
            ['name' => 'Create Tags',            'slug' => 'create-tags',            'group' => 'tags'],
            ['name' => 'Edit Tags',              'slug' => 'edit-tags',              'group' => 'tags'],
            ['name' => 'Delete Tags',            'slug' => 'delete-tags',            'group' => 'tags'],

            // --- Settings (Super Admin only) ---
            ['name' => 'View Settings',          'slug' => 'view-settings',          'group' => 'settings'],
            ['name' => 'Edit Settings',          'slug' => 'edit-settings',          'group' => 'settings'],

            // --- Reports ---
            ['name' => 'View Reports',           'slug' => 'view-reports',           'group' => 'reports'],
            ['name' => 'Export Reports',         'slug' => 'export-reports',         'group' => 'reports'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['slug' => $permission['slug']], $permission);
        }

        $all = Permission::all();

        // Helper to get permissions by slugs
        $perms = fn(array $slugs) => $all->filter(fn($p) => in_array($p->slug, $slugs));

        // =========================================================
        // SUPER ADMIN — All permissions
        // =========================================================
        $superAdmin->permissions()->sync($all);

        // =========================================================
        // ADMIN — All except role management
        // =========================================================
        $adminPerms = $all->filter(fn($p) => $p->group !== 'roles');
        $admin->permissions()->sync($adminPerms);

        // =========================================================
        // BENDAHARA — Financial (full), SPMB (view), Students (view),
        //             Reports, Announcements, Notifications
        // =========================================================
        $bendahara->permissions()->sync($perms([
            'view-dashboard-bendahara',
            // Students — view only
            'view-students',
            // SPMB — view + verify (untuk validasi pembayaran)
            'view-spmb', 'verify-spmb',
            // Financial — full
            'view-financial', 'manage-financial',
            // Reports
            'view-reports', 'export-reports',
            // Announcements — view only
            'view-announcements',
            // Notifications
            'view-notifications',
        ]));

        // =========================================================
        // GURU — Learning, Attendance, Grading, Announcements,
        //        Gallery, Posts (view), Students (view)
        // =========================================================
        $guru->permissions()->sync($perms([
            'view-dashboard-guru',
            // Students — view only (untuk keperluan mengajar)
            'view-students',
            // Classes — view only
            'view-classes',
            // Learning & CBT — full (materi, tugas, ujian, nilai)
            'view-learning', 'manage-learning',
            // Attendance — full
            'view-attendance', 'manage-attendance',
            // Announcements — full
            'view-announcements', 'create-announcements', 'edit-announcements', 'delete-announcements',
            // Gallery — view & upload
            'view-gallery', 'create-gallery', 'edit-gallery',
            // Posts — view only
            'view-posts',
            // Content — view (agar menu dropdown tampil)
            'view-content',
            // Reports — view only
            'view-reports',
            // Notifications
            'view-notifications',
        ]));

        // =========================================================
        // OPERATOR (TU) — Student data entry, SPMB, Academic structure,
        //                  Attendance view, Content entry
        // =========================================================
        $operator->permissions()->sync($perms([
            'view-dashboard-admin',
            // Students — full CRUD
            'view-students', 'create-students', 'edit-students', 'delete-students',
            // Classes — full CRUD
            'view-classes', 'create-classes', 'edit-classes', 'delete-classes',
            // Majors — full CRUD
            'view-majors', 'create-majors', 'edit-majors', 'delete-majors',
            // Academic Years — full CRUD
            'view-academic-years', 'create-academic-years', 'edit-academic-years', 'delete-academic-years',
            // SPMB — full CRUD + verify
            'view-spmb', 'create-spmb', 'edit-spmb', 'delete-spmb', 'verify-spmb',
            // Attendance — view & manage
            'view-attendance', 'manage-attendance',
            // Content — posts & announcements
            'view-content', 'view-posts', 'create-posts', 'edit-posts',
            'view-announcements', 'create-announcements', 'edit-announcements',
            'view-gallery', 'create-gallery',
            // Broadcast — view
            'view-broadcast',
            // Notifications
            'view-notifications',
            // Reports — view
            'view-reports',
        ]));

        // =========================================================
        // GURU BK — BK Counseling, Violations, Assessments, Students (CRUD),
        //           Classes (CRUD), Majors (CRUD)
        // =========================================================
        $guruBk->permissions()->sync($perms([
            'view-dashboard-bk',
            // Students — full CRUD
            'view-students', 'create-students', 'edit-students', 'delete-students',
            // Classes — full CRUD
            'view-classes', 'create-classes', 'edit-classes', 'delete-classes',
            // Majors — full CRUD
            'view-majors', 'create-majors', 'edit-majors', 'delete-majors',
            // BK — full
            'view-bk', 'manage-bk',
            // Announcements
            'view-announcements', 'create-announcements',
            // Notifications
            'view-notifications',
        ]));

        // =========================================================
        // STAFF — Minimal: dashboard, announcements, notifications
        // =========================================================
        $staff->permissions()->sync($perms([
            'view-dashboard',
            'view-announcements',
            'view-notifications',
        ]));

        // =========================================================
        // STUDENT / CALON SISWA — Dashboard only (portal siswa)
        // =========================================================
        $studentPerms = $perms(['view-dashboard']);
        $student->permissions()->sync($studentPerms);
        $calonSiswa->permissions()->sync($studentPerms);

        // =========================================================
        // KANTIN — Canteen Admin Dashboard & Order Management
        // =========================================================
        $canteenPerms = $perms([
            'view-dashboard',
            'view-canteen-admin',
            'manage-canteen-admin',
            'view-notifications',
        ]);
        $kantin->permissions()->sync($canteenPerms);
    }
}
