<?php

use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\AssignmentSubmissionController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EmailController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\GradeController;
use App\Http\Controllers\Admin\MajorController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\SpmbController;
use App\Http\Controllers\Admin\SpmbSettingController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WaveController;
use App\Http\Controllers\Admin\PaymentPostController;
use App\Http\Controllers\Admin\PaymentBillController;
use App\Http\Controllers\Admin\StudentPaymentController;
use App\Http\Controllers\Admin\StudentSavingsController;
use App\Http\Controllers\Admin\BankAccountController;
use App\Http\Controllers\Admin\FinancialCategoryController;
use App\Http\Controllers\Admin\FinancialTransactionController;
use App\Http\Controllers\Admin\FinancialReportController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\WaBroadcastController;
use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\ExamController as AdminExamController;
use App\Http\Controllers\Admin\AdminCanteenController;
use App\Http\Controllers\Admin\DatabaseMaintenanceController;
use App\Http\Controllers\Admin\CbtCapacityController;
use App\Http\Controllers\Student\StudentCanteenController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\QrAttendanceController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Blog\BlogController;
use App\Http\Controllers\Landing\LandingController;
use App\Http\Controllers\Spmb\DashboardController as SpmbDashboardController;
use App\Http\Controllers\Spmb\RegisterController;
use App\Http\Controllers\Student\AssignmentController as StudentAssignmentController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\GradeController as StudentGradeController;
use App\Http\Controllers\Student\MaterialController as StudentMaterialController;
use App\Http\Controllers\Student\ProfileController;
use App\Http\Controllers\Student\ScheduleController as StudentScheduleController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\SecurityCaptchaController;
use App\Http\Controllers\Admin\GuideController;
use App\Http\Controllers\PwaController;
use Illuminate\Support\Facades\Route;

// PWA Dynamic Manifest, Icons & Service Worker
Route::get('/manifest.json', [PwaController::class, 'manifest'])->name('pwa.manifest');
Route::get('/pwa-icon/{size?}', [PwaController::class, 'icon'])->name('pwa.icon');
Route::get('/sw.js', [PwaController::class, 'serviceWorker'])->name('pwa.sw');


/*
|--------------------------------------------------------------------------
| Setup Wizard / Installer Routes
|--------------------------------------------------------------------------
*/
Route::prefix('install')->group(function () {
    Route::get('/', [InstallController::class, 'index'])->name('install.index');
    Route::post('/test-db', [InstallController::class, 'testDatabase'])->name('install.test-db');
    Route::post('/process', [InstallController::class, 'process'])->name('install.process');
});

/*
|--------------------------------------------------------------------------
| Landing Page Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingController::class, 'home'])->name('home');
Route::get('/tentang', [LandingController::class, 'about'])->name('about');
Route::get('/berita', [LandingController::class, 'blog'])->name('blog');
Route::get('/berita/{slug}', [LandingController::class, 'blogShow'])->name('blog.show');
Route::get('/galeri', [LandingController::class, 'gallery'])->name('gallery');
Route::get('/kurikulum', [LandingController::class, 'curriculum'])->name('curriculum');
Route::get('/jurusan', [LandingController::class, 'majors'])->name('majors');
Route::get('/ekstrakurikuler', [LandingController::class, 'extracurricular'])->name('extracurricular');
Route::get('/kegiatan', [LandingController::class, 'events'])->name('events');
Route::get('/kegiatan/{id}', [LandingController::class, 'eventsShow'])->name('events.show');
Route::get('/kontak-kami', [LandingController::class, 'contact'])->name('contact');
Route::get('/kebijakan-privasi', [LandingController::class, 'privacyPolicy'])->name('privacy.policy');
Route::get('/syarat-ketentuan', [LandingController::class, 'termsConditions'])->name('terms.conditions');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Admin/Staff Login (Redirected to combined student/staff login page)
    Route::redirect('admin/login', '/login')->name('admin.login');
    Route::post('admin/login', [LoginController::class, 'adminLogin'])->name('admin.login.submit');
    Route::get('admin/verify-otp', [LoginController::class, 'showAdminOtpForm'])->name('admin.verify-otp');
    Route::post('admin/verify-otp', [LoginController::class, 'verifyAdminOtp'])->name('admin.verify-otp.submit');

    // Main Login (Combined Student/Staff/Admin Login)
    Route::get('login', [LoginController::class, 'showStudentLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'studentLogin'])->name('login.submit');
    Route::post('login-nisn', [LoginController::class, 'studentLoginNisn'])->name('login-nisn.submit');
    Route::get('verify-otp', [LoginController::class, 'showStudentOtpForm'])->name('verify-otp');
    Route::post('verify-otp', [LoginController::class, 'verifyStudentOtp'])->name('verify-otp.submit');
    Route::get('captcha/refresh', [SecurityCaptchaController::class, 'refresh'])->name('captcha.refresh');

    // Forgot Password
    Route::get('forgot-password', [ForgotPasswordController::class, 'showForgotPasswordForm'])->name('forgot-password');
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('forgot-password.submit');

    // Reset Password
    Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('reset-password');
    Route::post('reset-password', [ForgotPasswordController::class, 'reset'])->name('reset-password.submit');
});

// Parent Portal Routes
Route::prefix('parent')->name('parent.')->group(function () {
    Route::get('login', [\App\Http\Controllers\Parent\ParentLoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [\App\Http\Controllers\Parent\ParentLoginController::class, 'login'])->name('login.submit');
    Route::post('send-otp', [\App\Http\Controllers\Parent\ParentLoginController::class, 'sendOtp'])->name('send-otp');
    Route::post('verify-otp', [\App\Http\Controllers\Parent\ParentLoginController::class, 'verifyOtp'])->name('verify-otp');
    Route::get('dashboard', [\App\Http\Controllers\Parent\ParentDashboardController::class, 'index'])->name('dashboard');
    Route::get('student-card/print', [\App\Http\Controllers\Parent\ParentDashboardController::class, 'printCard'])->name('student-card.print');
    Route::get('raport/print', [\App\Http\Controllers\Parent\ParentDashboardController::class, 'printRaport'])->name('raport.print');
    Route::post('logout', [\App\Http\Controllers\Parent\ParentLoginController::class, 'logout'])->name('logout');
});

// Logout - POST for actual logout, GET for redirect to login
Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');
Route::get('logout', [LoginController::class, 'logoutConfirm'])->middleware('auth');

// Safety alias for profile-settings route
Route::get('profile-settings', function () {
    return redirect()->route('admin.profile');
})->name('profile-settings')->middleware('auth');


/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:super-admin|admin|guru|teacher|guru-quran|kepala-sekolah|wakasek-kesiswaan|wakasek-kurikulum|wakasek-kehumasan|bendahara|operator|kantin|guru-bk|bk|staff|tata-usaha'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard (Open to all admin roles)
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // User Guide / Panduan Admin
    Route::get('guide', [GuideController::class, 'index'])->name('guide');

    // Profile (Open to all admin roles)
    Route::get('profile', [SettingController::class, 'profile'])->name('profile');
    Route::put('profile', [SettingController::class, 'updateProfile'])->name('profile.update');

    // System Security & Audit Log
    Route::middleware('permission:view-settings')->group(function () {
        Route::get('security', [SecurityController::class, 'index'])->name('security.index');
        Route::post('security/unlock-user/{id}', [SecurityController::class, 'unlockUser'])->name('security.unlock-user');
        Route::post('security/block-ip', [SecurityController::class, 'blockIp'])->name('security.block-ip');
        Route::post('security/unlock-ip', [SecurityController::class, 'unlockIp'])->name('security.unlock-ip');
        Route::put('security/settings', [SecurityController::class, 'updateSettings'])->name('security.settings.update');
    });

    // Notifications
    Route::middleware('permission:view-notifications')->group(function () {
        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications/mark-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-read');
    });
    
    // Users
    Route::middleware('permission:create-users')->group(function () {
        Route::get('users/template', [UserController::class, 'downloadTemplate'])->name('users.template');
        Route::post('users/import', [UserController::class, 'import'])->name('users.import');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
    });
    Route::middleware('permission:view-users')->group(function () {
        Route::get('users/export', [UserController::class, 'export'])->name('users.export');
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    });

    Route::middleware('permission:edit-users')->group(function () {
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::post('users/{encodedId}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    });
    Route::middleware('permission:delete-users')->group(function () {
        Route::post('users/bulk-destroy', [UserController::class, 'bulkDestroy'])->name('users.bulk-destroy');
        Route::delete('users/bulk-destroy', [UserController::class, 'bulkDestroy']);
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
    
    // Roles & Permissions
    Route::middleware('permission:create-roles')->group(function () {
        Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
    });
    Route::middleware('permission:view-roles')->group(function () {
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('roles/{role}', [RoleController::class, 'show'])->name('roles.show');
    });
    Route::middleware('permission:edit-roles')->group(function () {
        Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    });
    Route::middleware('permission:delete-roles')->group(function () {
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });
    
    // Students - route statis (create, export, import, template, bulk-edit, bulk-update) harus di atas route dinamis ({student})
    Route::middleware('permission:edit-students')->group(function () {
        Route::get('students/bulk-edit', [StudentController::class, 'bulkEdit'])->name('students.bulk-edit');
        Route::post('students/bulk-update', [StudentController::class, 'bulkUpdate'])->name('students.bulk-update');
    });
    Route::middleware('permission:create-students')->group(function () {
        Route::get('students/create', [StudentController::class, 'create'])->name('students.create');
        Route::post('students', [StudentController::class, 'store'])->name('students.store');
        Route::post('students/import', [StudentController::class, 'import'])->name('students.import');
    });
    Route::middleware('permission:view-students')->group(function () {
        // Student Face ID Registration
        Route::get('students/face-id', [StudentController::class, 'faceIdIndex'])->name('students.face-id.index');
        Route::post('students/face-id', [StudentController::class, 'storeFaceId'])->name('students.face-id.store');
        Route::delete('students/{student}/face-id', [StudentController::class, 'destroyFaceId'])->name('students.face-id.destroy');

        Route::get('students/export', [StudentController::class, 'export'])->name('students.export');
        Route::get('students/download-template', [StudentController::class, 'downloadTemplate'])->name('students.download-template');
        Route::get('students', [StudentController::class, 'index'])->name('students.index');
        Route::get('students/{student}', [StudentController::class, 'show'])->name('students.show');
        Route::get('students/{encodedId}/print-card', [StudentController::class, 'printCard'])->name('students.print-card');

        
        // Student ID Card Print & Custom Settings
        Route::get('student-cards', [\App\Http\Controllers\Admin\StudentCardController::class, 'index'])->name('student-cards.index');
        Route::get('student-cards/settings', [\App\Http\Controllers\Admin\StudentCardController::class, 'settings'])->name('student-cards.settings');
        Route::put('student-cards/settings', [\App\Http\Controllers\Admin\StudentCardController::class, 'updateSettings'])->name('student-cards.update-settings');
        Route::get('student-cards/print', [\App\Http\Controllers\Admin\StudentCardController::class, 'print'])->name('student-cards.print');
        Route::post('student-cards/print-bulk', [\App\Http\Controllers\Admin\StudentCardController::class, 'print'])->name('student-cards.print-bulk');
        // Alumni Directory & Graduation
        Route::get('alumni', [\App\Http\Controllers\Admin\AlumniController::class, 'index'])->name('alumni.index');
        Route::get('alumni/export', [\App\Http\Controllers\Admin\AlumniController::class, 'export'])->name('alumni.export');
    });
    Route::middleware('permission:edit-students')->group(function () {
        Route::post('students/{student}/toggle-status', [StudentController::class, 'toggleStatus'])->name('students.toggle-status');
        Route::get('students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
        Route::put('students/{student}', [StudentController::class, 'update'])->name('students.update');

        Route::post('alumni/graduate-class', [\App\Http\Controllers\Admin\AlumniController::class, 'graduateClass'])->name('alumni.graduate-class');
        Route::post('alumni/graduate-selected', [\App\Http\Controllers\Admin\AlumniController::class, 'graduateSelected'])->name('alumni.graduate-selected');
        Route::post('alumni/{student}/revert-status', [\App\Http\Controllers\Admin\AlumniController::class, 'revertStatus'])->name('alumni.revert-status');
    });
    Route::middleware('permission:delete-students')->group(function () {
        Route::delete('students/bulk-delete', [StudentController::class, 'bulkDestroy'])->name('students.bulk-destroy');
        Route::delete('students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
    });

    
    // SPMB
    Route::middleware('permission:view-spmb')->group(function () {
        Route::get('spmb', [SpmbController::class, 'index'])->name('spmb.index');
        Route::get('spmb/{encodedId}', [SpmbController::class, 'show'])->name('spmb.show');
        Route::get('spmb/{encodedId}/print', [SpmbController::class, 'printForm'])->name('spmb.print');
        
        Route::get('spmb-settings', [SpmbSettingController::class, 'edit'])->name('spmb.settings');
        Route::put('spmb-settings', [SpmbSettingController::class, 'update'])->name('spmb.update-settings');
        
        // Waves View
        Route::get('waves', [WaveController::class, 'index'])->name('waves.index');
        Route::get('waves/{wave}', [WaveController::class, 'show'])->name('waves.show');
    });
    Route::middleware('permission:create-spmb')->group(function () {
        Route::get('waves/create', [WaveController::class, 'create'])->name('waves.create');
        Route::post('waves', [WaveController::class, 'store'])->name('waves.store');
    });
    Route::middleware('permission:edit-spmb')->group(function () {
        Route::get('waves/{wave}/edit', [WaveController::class, 'edit'])->name('waves.edit');
        Route::put('waves/{wave}', [WaveController::class, 'update'])->name('waves.update');
    });
    Route::middleware('permission:delete-spmb')->group(function () {
        Route::delete('waves/{wave}', [WaveController::class, 'destroy'])->name('waves.destroy');
        Route::delete('spmb/bulk-delete', [SpmbController::class, 'bulkDestroy'])->name('spmb.bulk-destroy');
        Route::delete('spmb/{encodedId}', [SpmbController::class, 'destroy'])->name('spmb.destroy');
    });

    Route::middleware('permission:verify-spmb')->group(function () {
        Route::post('spmb/{encodedId}/verify', [SpmbController::class, 'verify'])->name('spmb.verify');
        Route::post('spmb/{encodedId}/confirm-payment', [SpmbController::class, 'confirmPayment'])->name('spmb.confirm-payment');
        Route::post('spmb/{encodedId}/accept', [SpmbController::class, 'accept'])->name('spmb.accept');
        Route::post('spmb/{encodedId}/reject', [SpmbController::class, 'reject'])->name('spmb.reject');
        Route::post('spmb/{encodedId}/reset-password', [SpmbController::class, 'resetPassword'])->name('spmb.reset-password');
    });
    
    // Classes
    Route::middleware('permission:view-classes')->group(function () {
        Route::get('classes', [ClassController::class, 'index'])->name('classes.index');
        Route::get('classes/{class}', [ClassController::class, 'show'])->name('classes.show');
    });
    Route::middleware('permission:create-classes')->group(function () {
        Route::get('classes/create', [ClassController::class, 'create'])->name('classes.create');
        Route::post('classes', [ClassController::class, 'store'])->name('classes.store');
    });
    Route::middleware('permission:edit-classes')->group(function () {
        Route::get('classes/{class}/edit', [ClassController::class, 'edit'])->name('classes.edit');
        Route::put('classes/{class}', [ClassController::class, 'update'])->name('classes.update');
    });
    Route::middleware('permission:delete-classes')->group(function () {
        Route::delete('classes/{class}', [ClassController::class, 'destroy'])->name('classes.destroy');
    });

    // Academic Years
    Route::middleware('permission:view-academic-years')->group(function () {
        Route::get('academic-years', [AcademicYearController::class, 'index'])->name('academic-years.index');
    });
    Route::middleware('permission:create-academic-years')->group(function () {
        Route::post('academic-years', [AcademicYearController::class, 'store'])->name('academic-years.store');
        Route::put('academic-years/{academicYear}', [AcademicYearController::class, 'update'])->name('academic-years.update');
        Route::delete('academic-years/{academicYear}', [AcademicYearController::class, 'destroy'])->name('academic-years.destroy');
        Route::post('academic-years/{academicYear}/set-active', [AcademicYearController::class, 'setActive'])->name('academic-years.set-active');
    });

    // Majors
    Route::middleware('permission:view-majors')->group(function () {
        Route::get('majors', [MajorController::class, 'index'])->name('majors.index');
        Route::get('majors/{major}', [MajorController::class, 'show'])->name('majors.show');
    });
    Route::middleware('permission:create-majors')->group(function () {
        Route::get('majors/create', [MajorController::class, 'create'])->name('majors.create');
        Route::post('majors', [MajorController::class, 'store'])->name('majors.store');
    });
    Route::middleware('permission:edit-majors')->group(function () {
        Route::get('majors/{major}/edit', [MajorController::class, 'edit'])->name('majors.edit');
        Route::put('majors/{major}', [MajorController::class, 'update'])->name('majors.update');
    });
    Route::middleware('permission:delete-majors')->group(function () {
        Route::delete('majors/{major}', [MajorController::class, 'destroy'])->name('majors.destroy');
    });

    // Master Subjects (Mata Pelajaran Global)
    Route::get('subjects', [SubjectController::class, 'index'])->name('subjects.index');
    Route::post('subjects', [SubjectController::class, 'store'])->name('subjects.store');
    Route::put('subjects/{subject}', [SubjectController::class, 'update'])->name('subjects.update');
    Route::patch('subjects/{subject}/toggle-status', [SubjectController::class, 'toggleStatus'])->name('subjects.toggle-status');
    Route::delete('subjects/{subject}', [SubjectController::class, 'destroy'])->name('subjects.destroy');

    // Categories
    Route::middleware('permission:view-categories')->group(function () {
        Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    });
    Route::middleware('permission:create-categories')->group(function () {
        Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    });
    Route::middleware('permission:edit-categories')->group(function () {
        Route::put('categories/{encodedId}', [CategoryController::class, 'update'])->name('categories.update');
        Route::post('categories/{encodedId}/toggle', [CategoryController::class, 'toggleStatus'])->name('categories.toggle');
    });
    Route::middleware('permission:delete-categories')->group(function () {
        Route::delete('categories/{encodedId}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });

    // Tags
    Route::middleware('permission:view-tags')->group(function () {
        Route::get('tags', [TagController::class, 'index'])->name('tags.index');
    });
    Route::middleware('permission:create-tags')->group(function () {
        Route::post('tags', [TagController::class, 'store'])->name('tags.store');
    });
    Route::middleware('permission:edit-tags')->group(function () {
        Route::put('tags/{id}', [TagController::class, 'update'])->name('tags.update');
    });
    Route::middleware('permission:delete-tags')->group(function () {
        Route::delete('tags/{id}', [TagController::class, 'destroy'])->name('tags.destroy');
    });
    
    // Posts/Blog
    Route::middleware('permission:create-posts')->group(function () {
        Route::get('posts/create', [PostController::class, 'create'])->name('posts.create');
        Route::post('posts', [PostController::class, 'store'])->name('posts.store');
    });
    Route::middleware('permission:view-posts')->group(function () {
        Route::get('posts', [PostController::class, 'index'])->name('posts.index');
        Route::get('posts/{post}', [PostController::class, 'show'])->name('posts.show');
    });
    Route::middleware('permission:edit-posts')->group(function () {
        Route::get('posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
        Route::put('posts/{post}', [PostController::class, 'update'])->name('posts.update');
    });
    Route::middleware('permission:delete-posts')->group(function () {
        Route::delete('posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    });
    
    // Gallery
    Route::middleware('permission:create-gallery')->group(function () {
        Route::get('gallery/create', [GalleryController::class, 'create'])->name('gallery.create');
        Route::post('gallery', [GalleryController::class, 'store'])->name('gallery.store');
    });
    Route::middleware('permission:view-gallery')->group(function () {
        Route::get('gallery', [GalleryController::class, 'index'])->name('gallery.index');
        Route::get('gallery/{gallery}', [GalleryController::class, 'show'])->name('gallery.show');
    });
    Route::middleware('permission:edit-gallery')->group(function () {
        Route::get('gallery/{gallery}/edit', [GalleryController::class, 'edit'])->name('gallery.edit');
        Route::put('gallery/{gallery}', [GalleryController::class, 'update'])->name('gallery.update');
    });
    Route::middleware('permission:delete-gallery')->group(function () {
        Route::delete('gallery/{gallery}', [GalleryController::class, 'destroy'])->name('gallery.destroy');
    });

    // Sliders
    Route::middleware('permission:view-content')->group(function () {
        Route::resource('sliders', SliderController::class);
        Route::resource('extracurriculars', \App\Http\Controllers\Admin\ExtracurricularController::class);
        Route::post('extracurriculars/{extracurricular}/toggle', [\App\Http\Controllers\Admin\ExtracurricularController::class, 'toggleStatus'])->name('extracurriculars.toggle');
        
        Route::get('curriculum', [\App\Http\Controllers\Admin\CurriculumController::class, 'index'])->name('curriculum.index');
        Route::put('curriculum/description', [\App\Http\Controllers\Admin\CurriculumController::class, 'updateDescription'])->name('curriculum.update-description');
        Route::get('curriculum/create', [\App\Http\Controllers\Admin\CurriculumController::class, 'create'])->name('curriculum.create');
        Route::post('curriculum', [\App\Http\Controllers\Admin\CurriculumController::class, 'store'])->name('curriculum.store');
        Route::get('curriculum/{curriculum}/edit', [\App\Http\Controllers\Admin\CurriculumController::class, 'edit'])->name('curriculum.edit');
        Route::put('curriculum/{curriculum}', [\App\Http\Controllers\Admin\CurriculumController::class, 'update'])->name('curriculum.update');
        Route::delete('curriculum/{curriculum}', [\App\Http\Controllers\Admin\CurriculumController::class, 'destroy'])->name('curriculum.destroy');
        Route::post('curriculum/{curriculum}/toggle', [\App\Http\Controllers\Admin\CurriculumController::class, 'toggleStatus'])->name('curriculum.toggle');
    });


    // Announcements
    Route::middleware('permission:create-announcements')->group(function () {
        Route::get('announcements/create', [AnnouncementController::class, 'create'])->name('announcements.create');
        Route::post('announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    });
    Route::middleware('permission:view-announcements')->group(function () {
        Route::get('announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
        Route::get('announcements/{announcement}', [AnnouncementController::class, 'show'])->name('announcements.show');
    });
    Route::middleware('permission:edit-announcements')->group(function () {
        Route::get('announcements/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('announcements.edit');
        Route::put('announcements/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update');
    });
    Route::middleware('permission:delete-announcements')->group(function () {
        Route::delete('announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    });

    // Student Features - Schedules, Assignments, Grades, Materials, Exams, Books
    Route::middleware('permission:view-learning')->group(function () {
        // Statis dahulu sebelum dinamis
        Route::get('schedules/create', [ScheduleController::class, 'create'])->name('schedules.create');
        Route::get('schedules', [ScheduleController::class, 'index'])->name('schedules.index');
        Route::get('schedules/{schedule}', [ScheduleController::class, 'show'])->name('schedules.show');
        
        Route::get('assignments/create', [AssignmentController::class, 'create'])->name('assignments.create');
        Route::get('assignments', [AssignmentController::class, 'index'])->name('assignments.index');
        Route::get('assignments/{assignment}', [AssignmentController::class, 'show'])->name('assignments.show');
        
        Route::get('materials/create', [MaterialController::class, 'create'])->name('materials.create');
        Route::get('materials', [MaterialController::class, 'index'])->name('materials.index');
        Route::get('materials/{material}', [MaterialController::class, 'show'])->name('materials.show');
        Route::get('materials/{material}/download', [MaterialController::class, 'download'])->name('materials.download');
        
        Route::get('grades/create', [GradeController::class, 'create'])->name('grades.create');
        Route::get('grades/bulk/create', [GradeController::class, 'bulkCreate'])->name('grades.bulk.create');
        Route::get('grades/download-template', [GradeController::class, 'downloadTemplate'])->name('grades.download.template');
        Route::get('grades/export/excel', [GradeController::class, 'exportExcel'])->name('grades.export.excel');
        Route::post('grades/import/excel', [GradeController::class, 'importExcel'])->name('grades.import.excel');
        Route::get('grades', [GradeController::class, 'index'])->name('grades.index');
        Route::post('grades/store-agenda', [GradeController::class, 'storeAgenda'])->name('grades.store-agenda');
        Route::delete('grades/agenda/{id}', [GradeController::class, 'destroyAgenda'])->name('grades.destroy-agenda');
        Route::get('grades/{grade}', [GradeController::class, 'show'])->name('grades.show');

        // Cetak Raport Siswa
        Route::get('raport', [\App\Http\Controllers\Admin\RaportController::class, 'index'])->name('raport.index');
        Route::get('raport/settings', [\App\Http\Controllers\Admin\RaportController::class, 'settings'])->name('raport.settings');
        Route::put('raport/settings', [\App\Http\Controllers\Admin\RaportController::class, 'updateSettings'])->name('raport.update-settings');
        Route::get('raport/print', [\App\Http\Controllers\Admin\RaportController::class, 'print'])->name('raport.print');
        Route::post('raport/print-bulk', [\App\Http\Controllers\Admin\RaportController::class, 'print'])->name('raport.print-bulk');

        // Modul Halaqah Al-Qur'an (Tahsin & Tahfidz Harian/Massal/Laporan/Grafik)
        Route::get('halaqah', [\App\Http\Controllers\Admin\HalaqahController::class, 'index'])->name('halaqah.index');
        Route::post('halaqah/store-individual', [\App\Http\Controllers\Admin\HalaqahController::class, 'storeIndividual'])->name('halaqah.store-individual');
        Route::post('halaqah/store-mass', [\App\Http\Controllers\Admin\HalaqahController::class, 'storeMass'])->name('halaqah.store-mass');
        Route::delete('halaqah/{id}', [\App\Http\Controllers\Admin\HalaqahController::class, 'destroy'])->name('halaqah.destroy');
        Route::get('halaqah/export-excel', [\App\Http\Controllers\Admin\HalaqahController::class, 'exportExcel'])->name('halaqah.export-excel');
        Route::get('halaqah/group-students', [\App\Http\Controllers\Admin\HalaqahController::class, 'getGroupStudents'])->name('halaqah.group-students');
        Route::post('halaqah/save-group', [\App\Http\Controllers\Admin\HalaqahController::class, 'saveGroup'])->name('halaqah.save-group');

        // Modul E-Raport Khusus Pembelajaran Al-Qur'an (Terpisah Sendiri)
        Route::get('quran-raport', [\App\Http\Controllers\Admin\QuranRaportController::class, 'index'])->name('quran-raport.index');
        Route::get('quran-raport/settings', [\App\Http\Controllers\Admin\QuranRaportController::class, 'settings'])->name('quran-raport.settings');
        Route::post('quran-raport/settings', [\App\Http\Controllers\Admin\QuranRaportController::class, 'saveSettings'])->name('quran-raport.settings.save');
        Route::get('quran-raport/preview', [\App\Http\Controllers\Admin\QuranRaportController::class, 'preview'])->name('quran-raport.preview');
        Route::get('quran-raport/print', [\App\Http\Controllers\Admin\QuranRaportController::class, 'print'])->name('quran-raport.print');
        Route::post('quran-raport/print-bulk', [\App\Http\Controllers\Admin\QuranRaportController::class, 'print'])->name('quran-raport.print-bulk');

        
        Route::get('exams/create', [AdminExamController::class, 'create'])->name('exams.create');
        Route::get('exams/download-questions-template', [AdminExamController::class, 'downloadQuestionsTemplate'])->name('exams.download-questions-template');
        Route::get('exams', [AdminExamController::class, 'index'])->name('exams.index');
        Route::get('cbt-capacity', [CbtCapacityController::class, 'index'])->name('cbt-capacity.index');
        Route::post('cbt-capacity/simulate', [CbtCapacityController::class, 'simulate'])->name('cbt-capacity.simulate');
        Route::post('cbt-capacity/optimize', [CbtCapacityController::class, 'optimize'])->name('cbt-capacity.optimize');
        Route::get('exams/{exam}', [AdminExamController::class, 'show'])->name('exams.show');
        
        Route::get('books/create', [AdminBookController::class, 'create'])->name('books.create');
        Route::get('books', [AdminBookController::class, 'index'])->name('books.index');
        Route::get('books/{book}', [AdminBookController::class, 'show'])->name('books.show');

        // LMS Management
        Route::get('lms/chapters', [\App\Http\Controllers\Admin\LmsChapterController::class, 'index'])->name('lms.chapters.index');
        Route::post('lms/chapters', [\App\Http\Controllers\Admin\LmsChapterController::class, 'storeChapter'])->name('lms.chapters.store');
        Route::put('lms/chapters/{chapter}', [\App\Http\Controllers\Admin\LmsChapterController::class, 'updateChapter'])->name('lms.chapters.update');
        Route::delete('lms/chapters/{chapter}', [\App\Http\Controllers\Admin\LmsChapterController::class, 'destroyChapter'])->name('lms.chapters.destroy');

        Route::post('lms/topics', [\App\Http\Controllers\Admin\LmsChapterController::class, 'storeTopic'])->name('lms.topics.store');
        Route::delete('lms/topics/{topic}', [\App\Http\Controllers\Admin\LmsChapterController::class, 'destroyTopic'])->name('lms.topics.destroy');

        Route::post('lms/quizzes', [\App\Http\Controllers\Admin\LmsChapterController::class, 'storeQuiz'])->name('lms.quizzes.store');
        Route::delete('lms/quizzes/{quiz}', [\App\Http\Controllers\Admin\LmsChapterController::class, 'destroyQuiz'])->name('lms.quizzes.destroy');

        Route::post('lms/topics/assignment', [\App\Http\Controllers\Admin\LmsChapterController::class, 'storeTopicAssignment'])->name('lms.topics.assignment.store');
        Route::post('lms/topics/exam', [\App\Http\Controllers\Admin\LmsChapterController::class, 'storeTopicExam'])->name('lms.topics.exam.store');
        Route::post('lms/topics/material', [\App\Http\Controllers\Admin\LmsChapterController::class, 'storeTopicMaterial'])->name('lms.topics.material.store');

        Route::post('lms/topics/link-assignment', [\App\Http\Controllers\Admin\LmsChapterController::class, 'linkAssignment'])->name('lms.topics.assignment.link');
        Route::post('lms/topics/link-exam', [\App\Http\Controllers\Admin\LmsChapterController::class, 'linkExam'])->name('lms.topics.exam.link');
        Route::post('lms/topics/link-material', [\App\Http\Controllers\Admin\LmsChapterController::class, 'linkMaterial'])->name('lms.topics.material.link');
        Route::delete('lms/topics/unlink-assignment/{id}', [\App\Http\Controllers\Admin\LmsChapterController::class, 'unlinkAssignment'])->name('lms.topics.assignment.unlink');
        Route::delete('lms/topics/unlink-exam/{id}', [\App\Http\Controllers\Admin\LmsChapterController::class, 'unlinkExam'])->name('lms.topics.exam.unlink');
        Route::delete('lms/topics/unlink-material/{id}', [\App\Http\Controllers\Admin\LmsChapterController::class, 'unlinkMaterial'])->name('lms.topics.material.unlink');

        Route::post('lms/live-classes', [\App\Http\Controllers\Admin\LmsChapterController::class, 'storeLiveClass'])->name('lms.live-classes.store');
        Route::put('lms/live-classes/{liveClass}/status', [\App\Http\Controllers\Admin\LmsChapterController::class, 'updateLiveStatus'])->name('lms.live-classes.status');
    });
    Route::middleware('permission:manage-learning')->group(function () {
        Route::post('schedules/bulk-delete', [ScheduleController::class, 'bulkDestroy'])->name('schedules.bulk-destroy');
        Route::delete('schedules/bulk-delete', [ScheduleController::class, 'bulkDestroy']);
        Route::post('schedules', [ScheduleController::class, 'store'])->name('schedules.store');
        Route::get('schedules/{schedule}/edit', [ScheduleController::class, 'edit'])->name('schedules.edit');
        Route::put('schedules/{schedule}', [ScheduleController::class, 'update'])->name('schedules.update');
        Route::delete('schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');
        
        Route::post('assignments', [AssignmentController::class, 'store'])->name('assignments.store');
        Route::get('assignments/{assignment}/edit', [AssignmentController::class, 'edit'])->name('assignments.edit');
        Route::put('assignments/{assignment}', [AssignmentController::class, 'update'])->name('assignments.update');
        Route::delete('assignments/{assignment}', [AssignmentController::class, 'destroy'])->name('assignments.destroy');
        
        Route::post('materials', [MaterialController::class, 'store'])->name('materials.store');
        Route::get('materials/{material}/edit', [MaterialController::class, 'edit'])->name('materials.edit');
        Route::put('materials/{material}', [MaterialController::class, 'update'])->name('materials.update');
        Route::delete('materials/{material}', [MaterialController::class, 'destroy'])->name('materials.destroy');
        
        Route::post('grades/bulk', [GradeController::class, 'bulkStore'])->name('grades.bulk.store');
        Route::post('grades', [GradeController::class, 'store'])->name('grades.store');
        Route::get('grades/{grade}/edit', [GradeController::class, 'edit'])->name('grades.edit');
        Route::put('grades/{grade}', [GradeController::class, 'update'])->name('grades.update');
        Route::delete('grades/{grade}', [GradeController::class, 'destroy'])->name('grades.destroy');
        
        Route::resource('assignment-submissions', AssignmentSubmissionController::class);
        Route::get('assignment-submissions/{submission}/grade', [AssignmentSubmissionController::class, 'grade'])->name('assignment-submissions.grade');
        Route::post('assignment-submissions/{submission}/grade', [AssignmentSubmissionController::class, 'storeGrade'])->name('assignment-submissions.store-grade');
        Route::get('assignment-submissions/{submission}/download', [AssignmentSubmissionController::class, 'download'])->name('assignment-submissions.download');
        
        Route::post('exams', [AdminExamController::class, 'store'])->name('exams.store');
        Route::get('exams/{exam}/edit', [AdminExamController::class, 'edit'])->name('exams.edit');
        Route::put('exams/{exam}', [AdminExamController::class, 'update'])->name('exams.update');
        Route::delete('exams/{exam}', [AdminExamController::class, 'destroy'])->name('exams.destroy');
        Route::get('exams/{exam}/grade', [AdminExamController::class, 'showGradeStudent'])->name('exams.grade-all');
        Route::post('exams/{exam}/grade', [AdminExamController::class, 'storeStudentGrade'])->name('exams.store-grade-all');
        Route::get('exams/{exam}/results/{result}/grade', [AdminExamController::class, 'showGradeStudent'])->name('exams.grade-student');
        Route::post('exams/{exam}/results/{result}/grade', [AdminExamController::class, 'storeStudentGrade'])->name('exams.store-student-grade');
        Route::post('exams/{exam}/questions', [AdminExamController::class, 'storeQuestion'])->name('exams.questions.store');
        Route::post('exams/{exam}/import-questions', [AdminExamController::class, 'importQuestions'])->name('exams.questions.import');
        Route::put('exams/{exam}/questions/{question}', [AdminExamController::class, 'updateQuestion'])->name('exams.questions.update');
        Route::delete('exams/{exam}/questions/{question}', [AdminExamController::class, 'destroyQuestion'])->name('exams.questions.destroy');
        
        Route::post('books', [AdminBookController::class, 'store'])->name('books.store');
        Route::get('books/{book}/edit', [AdminBookController::class, 'edit'])->name('books.edit');
        Route::put('books/{book}', [AdminBookController::class, 'update'])->name('books.update');
        Route::delete('books/{book}', [AdminBookController::class, 'destroy'])->name('books.destroy');
        Route::post('books/{book}/toggle', [AdminBookController::class, 'toggleStatus'])->name('books.toggle');
    });

    // Attendance
    Route::middleware('permission:view-attendance')->group(function () {
        // Statis dahulu sebelum dinamis
        Route::get('attendances/settings', [AttendanceController::class, 'settings'])->name('attendances.settings');
        Route::get('attendances/create', [AttendanceController::class, 'create'])->name('attendances.create');
        Route::get('attendances/print', [AttendanceController::class, 'printReport'])->name('attendances.print');
        Route::get('attendances', [AttendanceController::class, 'index'])->name('attendances.index');
        Route::get('attendances/{attendance}', [AttendanceController::class, 'show'])->name('attendances.show');
        Route::get('qr-attendance/history', [QrAttendanceController::class, 'history'])->name('qr-attendance.history');
        Route::get('qr-attendance/summary', [QrAttendanceController::class, 'todaySummary'])->name('qr-attendance.summary');

        // Student Permits (Permohonan Izin Siswa Admin)
        Route::get('student-permits', [\App\Http\Controllers\Admin\StudentPermitController::class, 'index'])->name('student-permits.index');
        Route::get('student-permits/create', [\App\Http\Controllers\Admin\StudentPermitController::class, 'create'])->name('student-permits.create');
        Route::post('student-permits', [\App\Http\Controllers\Admin\StudentPermitController::class, 'store'])->name('student-permits.store');
        Route::post('student-permits/{permit}/approve', [\App\Http\Controllers\Admin\StudentPermitController::class, 'approve'])->name('student-permits.approve');
        Route::post('student-permits/{permit}/reject', [\App\Http\Controllers\Admin\StudentPermitController::class, 'reject'])->name('student-permits.reject');
        Route::delete('student-permits/{permit}', [\App\Http\Controllers\Admin\StudentPermitController::class, 'destroy'])->name('student-permits.destroy');

        // Teacher Attendance - Admin Management (Presensi Guru & Staff)
        Route::get('teacher-attendances/fingerprint', [\App\Http\Controllers\Admin\TeacherAttendanceController::class, 'fingerprintPage'])->name('teacher-attendances.fingerprint');
        Route::post('teacher-attendances/fingerprint/register', [\App\Http\Controllers\Admin\TeacherAttendanceController::class, 'registerFingerprint'])->name('teacher-attendances.fingerprint.register');
        Route::post('teacher-attendances/fingerprint/verify', [\App\Http\Controllers\Admin\TeacherAttendanceController::class, 'verifyFingerprint'])->name('teacher-attendances.fingerprint.verify');

        Route::get('teacher-attendances/mobile', function () {
            return redirect()->route('admin.teacher-attendances.my-attendance');
        })->name('teacher-attendances.mobile');
        Route::post('teacher-attendances/update-session-times', [\App\Http\Controllers\Admin\TeacherAttendanceController::class, 'updateSessionTimes'])->name('teacher-attendances.update-session-times');

        Route::get('teacher-attendances/scan', [\App\Http\Controllers\Admin\TeacherAttendanceController::class, 'scanFace'])->name('teacher-attendances.scan');
        Route::get('teacher-attendances/register-face', [\App\Http\Controllers\Admin\TeacherAttendanceController::class, 'registerFacePage'])->name('teacher-attendances.register-face-page');
        Route::get('teacher-attendances/export', [\App\Http\Controllers\Admin\TeacherAttendanceController::class, 'export'])->name('teacher-attendances.export');
        Route::get('teacher-attendances/recap', [\App\Http\Controllers\Admin\TeacherAttendanceController::class, 'recap'])->name('teacher-attendances.recap');
        Route::get('teacher-attendances', [\App\Http\Controllers\Admin\TeacherAttendanceController::class, 'index'])->name('teacher-attendances.index');

        Route::post('teacher-attendances', [\App\Http\Controllers\Admin\TeacherAttendanceController::class, 'store'])->name('teacher-attendances.store');
        Route::post('teacher-attendances/register-face', [\App\Http\Controllers\Admin\TeacherAttendanceController::class, 'registerFace'])->name('teacher-attendances.register-face');
        Route::post('teacher-attendances/verify-face', [\App\Http\Controllers\Admin\TeacherAttendanceController::class, 'verifyFace'])->name('teacher-attendances.verify-face');

        // Teacher Attendance Settings Module
        Route::get('teacher-attendances/settings', [\App\Http\Controllers\Admin\TeacherAttendanceController::class, 'settings'])->name('teacher-attendances.settings');
        Route::put('teacher-attendances/settings', [\App\Http\Controllers\Admin\TeacherAttendanceController::class, 'updateSettings'])->name('teacher-attendances.update-settings');

        Route::get('teacher-attendances/stream', [\App\Http\Controllers\Admin\TeacherAttendanceController::class, 'streamEvents'])->name('teacher-attendances.stream');
        Route::post('teacher-attendances/fingerprint/device-event', [\App\Http\Controllers\Admin\TeacherAttendanceController::class, 'logDeviceEvent'])->name('teacher-attendances.fingerprint.device-event');

        Route::delete('teacher-attendances/{id}', [\App\Http\Controllers\Admin\TeacherAttendanceController::class, 'destroy'])->name('teacher-attendances.destroy');
    });

    // Fallback non-prefixed alias routes for stream and events
    Route::get('admin-stream-fallback', [\App\Http\Controllers\Admin\TeacherAttendanceController::class, 'streamEvents'])->name('teacher-attendances.stream.fallback');

    Route::get('teacher-attendances/my-attendance', [\App\Http\Controllers\Admin\TeacherAttendanceController::class, 'myAttendance'])->name('teacher-attendances.my-attendance');
    Route::post('teacher-attendances/self-checkin', [\App\Http\Controllers\Admin\TeacherAttendanceController::class, 'selfCheckIn'])->name('teacher-attendances.self-checkin');
    Route::get('teacher-attendances/check-status', [\App\Http\Controllers\Admin\TeacherAttendanceController::class, 'checkTodayStatus'])->name('teacher-attendances.check-status');
    Route::post('teacher-attendances/toggle-briefing', [\App\Http\Controllers\Admin\TeacherAttendanceController::class, 'toggleBriefing'])->name('teacher-attendances.toggle-briefing');
    Route::post('teacher-attendances/attend-briefing', [\App\Http\Controllers\Admin\TeacherAttendanceController::class, 'attendBriefing'])->name('teacher-attendances.attend-briefing');

    // Employee Tasks & Daily Checklist
    Route::get('employee-tasks', [\App\Http\Controllers\Admin\EmployeeTaskController::class, 'index'])->name('employee-tasks.index');
    Route::post('employee-tasks', [\App\Http\Controllers\Admin\EmployeeTaskController::class, 'store'])->name('employee-tasks.store');
    Route::put('employee-tasks/{id}', [\App\Http\Controllers\Admin\EmployeeTaskController::class, 'update'])->name('employee-tasks.update');
    Route::delete('employee-tasks/{id}', [\App\Http\Controllers\Admin\EmployeeTaskController::class, 'destroy'])->name('employee-tasks.destroy');
    Route::post('employee-tasks/{id}/toggle', [\App\Http\Controllers\Admin\EmployeeTaskController::class, 'toggleChecklist'])->name('employee-tasks.toggle');

    // Employee Leave & Permit Management (Pengajuan Izin Pegawai & Verifikasi Kepala Sekolah)
    Route::get('employee-permits', [\App\Http\Controllers\Admin\EmployeePermitController::class, 'index'])->name('employee-permits.index');
    Route::get('employee-permits/create', [\App\Http\Controllers\Admin\EmployeePermitController::class, 'create'])->name('employee-permits.create');
    Route::post('employee-permits', [\App\Http\Controllers\Admin\EmployeePermitController::class, 'store'])->name('employee-permits.store');
    Route::post('employee-permits/{permit}/approve', [\App\Http\Controllers\Admin\EmployeePermitController::class, 'approve'])->name('employee-permits.approve');
    Route::post('employee-permits/{permit}/reject', [\App\Http\Controllers\Admin\EmployeePermitController::class, 'reject'])->name('employee-permits.reject');
    Route::delete('employee-permits/{permit}', [\App\Http\Controllers\Admin\EmployeePermitController::class, 'destroy'])->name('employee-permits.destroy');

    // Kajian Pekanan Pegawai
    Route::get('kajian-pekanan', [\App\Http\Controllers\Admin\KajianPekananController::class, 'index'])->name('kajian-pekanan.index');
    Route::post('kajian-pekanan', [\App\Http\Controllers\Admin\KajianPekananController::class, 'store'])->name('kajian-pekanan.store');
    Route::get('kajian-pekanan/{kajian_pekanan}', [\App\Http\Controllers\Admin\KajianPekananController::class, 'show'])->name('kajian-pekanan.show');
    Route::put('kajian-pekanan/{kajian_pekanan}', [\App\Http\Controllers\Admin\KajianPekananController::class, 'update'])->name('kajian-pekanan.update');
    Route::put('kajian-pekanan/{kajian_pekanan}/attendance', [\App\Http\Controllers\Admin\KajianPekananController::class, 'updateAttendance'])->name('kajian-pekanan.attendance');
    Route::delete('kajian-pekanan/{kajian_pekanan}', [\App\Http\Controllers\Admin\KajianPekananController::class, 'destroy'])->name('kajian-pekanan.destroy');
    Route::get('kajian-pekanan/{kajian_pekanan}/print', [\App\Http\Controllers\Admin\KajianPekananController::class, 'printReport'])->name('kajian-pekanan.print');

    // Penilaian Kinerja Guru & Pegawai (KPI & Performance Engine)
    Route::get('kpi', [\App\Http\Controllers\Admin\KpiController::class, 'index'])->name('kpi.index');
    Route::get('kpi/evaluation-data/{userId}', [\App\Http\Controllers\Admin\KpiController::class, 'getEvaluationData'])->name('kpi.evaluation-data');
    Route::post('kpi/evaluation', [\App\Http\Controllers\Admin\KpiController::class, 'saveEvaluation'])->name('kpi.save-evaluation');
    Route::put('kpi/settings', [\App\Http\Controllers\Admin\KpiController::class, 'updateSettings'])->name('kpi.update-settings');
    Route::get('kpi/raport/{userId}', [\App\Http\Controllers\Admin\KpiController::class, 'showRaport'])->name('kpi.raport');
    Route::get('kpi/raport/{userId}/print', [\App\Http\Controllers\Admin\KpiController::class, 'printRaport'])->name('kpi.raport.print');
    Route::get('kpi/export', [\App\Http\Controllers\Admin\KpiController::class, 'exportExcel'])->name('kpi.export');

    // Mutabaah Ibadah Harian Pegawai & Guru
    Route::get('employee-mutabaah', [\App\Http\Controllers\Admin\EmployeeMutabaahController::class, 'index'])->name('employee-mutabaah.index');
    Route::post('employee-mutabaah', [\App\Http\Controllers\Admin\EmployeeMutabaahController::class, 'store'])->name('employee-mutabaah.store');
    Route::get('employee-mutabaah/recap', [\App\Http\Controllers\Admin\EmployeeMutabaahController::class, 'recap'])->name('employee-mutabaah.recap');


    // Aspirasi, Kritik & Saran Sekolah
    Route::get('feedback', [\App\Http\Controllers\Admin\FeedbackController::class, 'index'])->name('feedback.index');
    Route::post('feedback', [\App\Http\Controllers\Admin\FeedbackController::class, 'store'])->name('feedback.store');
    Route::patch('feedback/{feedback}/status', [\App\Http\Controllers\Admin\FeedbackController::class, 'updateStatus'])->name('feedback.status');
    Route::delete('feedback/{feedback}', [\App\Http\Controllers\Admin\FeedbackController::class, 'destroy'])->name('feedback.destroy');

    // Bimbingan & Konseling (BK)
    Route::middleware('permission:view-bk|manage-bk')->group(function () {
        Route::get('bk', [\App\Http\Controllers\Admin\BkCounselingController::class, 'index'])->name('bk.index');
        Route::get('bk/create', [\App\Http\Controllers\Admin\BkCounselingController::class, 'create'])->name('bk.create');
        Route::post('bk', [\App\Http\Controllers\Admin\BkCounselingController::class, 'store'])->name('bk.store');
        Route::get('bk/assessments', [\App\Http\Controllers\Admin\BkCounselingController::class, 'assessments'])->name('bk.assessments');
        Route::get('bk/assessments-index', [\App\Http\Controllers\Admin\BkCounselingController::class, 'assessments'])->name('bk.assessments.index');
        Route::post('bk/assessments', [\App\Http\Controllers\Admin\BkCounselingController::class, 'storeAssessment'])->name('bk.assessments.store');
        
        // BK Violations (Pelanggaran Siswa & Master Kategori)
        Route::get('bk/violations', [\App\Http\Controllers\Admin\BkViolationController::class, 'index'])->name('bk.violations.index');
        Route::get('bk/violations/create', [\App\Http\Controllers\Admin\BkViolationController::class, 'create'])->name('bk.violations.create');
        Route::post('bk/violations', [\App\Http\Controllers\Admin\BkViolationController::class, 'store'])->name('bk.violations.store');
        Route::get('bk/violations/categories', [\App\Http\Controllers\Admin\BkViolationController::class, 'categories'])->name('bk.violations.categories');
        Route::post('bk/violations/categories', [\App\Http\Controllers\Admin\BkViolationController::class, 'storeCategory'])->name('bk.violations.categories.store');
        Route::put('bk/violations/categories/{id}', [\App\Http\Controllers\Admin\BkViolationController::class, 'updateCategory'])->name('bk.violations.categories.update');
        Route::delete('bk/violations/categories/{id}', [\App\Http\Controllers\Admin\BkViolationController::class, 'destroyCategory'])->name('bk.violations.categories.destroy');
        Route::get('bk/violations/{id}', [\App\Http\Controllers\Admin\BkViolationController::class, 'show'])->name('bk.violations.show');
        Route::get('bk/violations/{id}/edit', [\App\Http\Controllers\Admin\BkViolationController::class, 'edit'])->name('bk.violations.edit');
        Route::put('bk/violations/{id}', [\App\Http\Controllers\Admin\BkViolationController::class, 'update'])->name('bk.violations.update');
        Route::delete('bk/violations/{id}', [\App\Http\Controllers\Admin\BkViolationController::class, 'destroy'])->name('bk.violations.destroy');

        Route::get('bk/{id}', [\App\Http\Controllers\Admin\BkCounselingController::class, 'show'])->name('bk.show');
        Route::put('bk/{id}', [\App\Http\Controllers\Admin\BkCounselingController::class, 'update'])->name('bk.update');
        Route::delete('bk/{id}', [\App\Http\Controllers\Admin\BkCounselingController::class, 'destroy'])->name('bk.destroy');
    });


    Route::middleware('permission:manage-attendance')->group(function () {
        Route::put('attendances/settings', [AttendanceController::class, 'updateSettings'])->name('attendances.update-settings');
        Route::post('attendances', [AttendanceController::class, 'store'])->name('attendances.store');
        Route::post('attendances/bulk', [AttendanceController::class, 'store'])->name('attendances.bulk.store');
        Route::get('attendances/{attendance}/edit', [AttendanceController::class, 'edit'])->name('attendances.edit');
        Route::put('attendances/{attendance}', [AttendanceController::class, 'update'])->name('attendances.update');
        Route::delete('attendances/{attendance}', [AttendanceController::class, 'destroy'])->name('attendances.destroy');
        
        Route::get('presensi_siswa', [QrAttendanceController::class, 'scan'])->name('qr-attendance.scan');
        Route::redirect('qr-attendance/scan', '/admin/presensi_siswa');
        Route::post('qr-attendance/process', [QrAttendanceController::class, 'processScan'])->name('qr-attendance.process');
    });

    // Financial & Payment Pos Module Routes
    Route::middleware('permission:view-financial')->group(function () {
        Route::get('student-payments', [StudentPaymentController::class, 'index'])->name('student-payments.index');
        Route::get('student-payments/tracking', [StudentPaymentController::class, 'tracking'])->name('student-payments.tracking');
        Route::get('student-payments/manual-confirm', [StudentPaymentController::class, 'manualConfirm'])->name('student-payments.manual-confirm');
        Route::get('student-payments/{student}/pay', [StudentPaymentController::class, 'pay'])->name('student-payments.pay');
        Route::post('student-payments/{student}/process', [StudentPaymentController::class, 'processPayment'])->name('student-payments.process');
        Route::post('student-payments/{student}/approve-payment/{transaction}', [StudentPaymentController::class, 'approvePayment'])->name('student-payments.approve-payment');
        Route::post('student-payments/{student}/reject-payment/{transaction}', [StudentPaymentController::class, 'rejectPayment'])->name('student-payments.reject-payment');
        Route::get('student-payments/receipt/{transaction}', [StudentPaymentController::class, 'receipt'])->name('student-payments.receipt');
        Route::get('student-payments/receipt/{transaction}/print', [StudentPaymentController::class, 'printReceipt'])->name('student-payments.print');
        Route::delete('student-payments/transactions/{transaction}', [StudentPaymentController::class, 'destroyTransaction'])->name('student-payments.destroy-transaction');
        Route::get('student-payments/{student}/print-history', [StudentPaymentController::class, 'printHistory'])->name('student-payments.print-history');
        Route::get('student-payments/{student}/print-history-receipt', [StudentPaymentController::class, 'printHistoryReceipt'])->name('student-payments.print-history-receipt');
        
        Route::get('savings', [StudentSavingsController::class, 'index'])->name('savings.index');
        Route::get('savings/manual-confirm', [StudentSavingsController::class, 'manualConfirm'])->name('savings.manual-confirm');
        Route::get('savings/{student}', [StudentSavingsController::class, 'show'])->name('savings.show');
        Route::post('savings/{student}/deposit', [StudentSavingsController::class, 'deposit'])->name('savings.deposit');
        Route::post('savings/{student}/withdraw', [StudentSavingsController::class, 'withdraw'])->name('savings.withdraw');
        Route::get('savings/{student}/print', [StudentSavingsController::class, 'printPassbook'])->name('savings.print');
        Route::post('savings/{student}/approve-deposit/{transaction}', [StudentSavingsController::class, 'approveDeposit'])->name('savings.approve-deposit');
        Route::post('savings/{student}/reject-deposit/{transaction}', [StudentSavingsController::class, 'rejectDeposit'])->name('savings.reject-deposit');
        
        Route::get('financial-reports', [FinancialReportController::class, 'index'])->name('financial-reports.index');
        Route::get('financial-reports/print', [FinancialReportController::class, 'print'])->name('financial-reports.print');
    });
    Route::middleware('permission:manage-financial')->group(function () {
        Route::resource('payment-posts', PaymentPostController::class);
        
        Route::resource('payment-bills', PaymentBillController::class);
        Route::post('payment-bills/{paymentBill}/generate', [PaymentBillController::class, 'generate'])->name('payment-bills.generate');
        Route::put('payment-bills/{paymentBill}/student-bills/{studentPaymentBill}', [PaymentBillController::class, 'updateStudentBill'])->name('payment-bills.student-bills.update');
        Route::delete('payment-bills/{paymentBill}/student-bills/{studentPaymentBill}', [PaymentBillController::class, 'destroyStudentBill'])->name('payment-bills.student-bills.destroy');
        Route::post('payment-bills/{paymentBill}/bulk-update-student-bills', [PaymentBillController::class, 'bulkUpdateStudentBills'])->name('payment-bills.bulk-update-student-bills');
        
        Route::resource('bank-accounts', BankAccountController::class);
        Route::resource('financial-categories', FinancialCategoryController::class);
        
        Route::get('financial-transactions/income', [FinancialTransactionController::class, 'income'])->name('financial-transactions.income');
        Route::get('financial-transactions/expense', [FinancialTransactionController::class, 'expense'])->name('financial-transactions.expense');
        Route::resource('financial-transactions', FinancialTransactionController::class);
    });

    // WhatsApp Gateway & Broadcast
    Route::middleware('permission:view-broadcast')->group(function () {
        Route::resource('wa-broadcasts', WaBroadcastController::class);
        Route::post('wa-gateway/test', [WaBroadcastController::class, 'sendTestMessage'])->name('wa-gateway.test');
    });

    // Digital Canteen POS & Management Routes
    Route::middleware('permission:view-canteen-admin')->group(function () {
        Route::get('canteen/orders', [AdminCanteenController::class, 'orders'])->name('canteen.orders');
        Route::post('canteen/orders/{id}/status', [AdminCanteenController::class, 'updateOrderStatus'])->name('canteen.orders.status');
        Route::get('canteen/items', [AdminCanteenController::class, 'items'])->name('canteen.items');
        Route::post('canteen/items', [AdminCanteenController::class, 'storeItem'])->name('canteen.items.store');
        Route::put('canteen/items/{id}', [AdminCanteenController::class, 'updateItem'])->name('canteen.items.update');
        Route::delete('canteen/items/{id}', [AdminCanteenController::class, 'destroyItem'])->name('canteen.items.destroy');
        Route::post('canteen/items/{id}/toggle', [AdminCanteenController::class, 'toggleItem'])->name('canteen.items.toggle');
        Route::get('canteen/reports', [AdminCanteenController::class, 'reports'])->name('canteen.reports');
        Route::get('canteen/reports/print', [AdminCanteenController::class, 'printReports'])->name('canteen.reports.print');
        
        Route::get('canteen/withdrawals', [AdminCanteenController::class, 'withdrawals'])->name('canteen.withdrawals');
        Route::post('canteen/withdrawals/{id}/approve', [AdminCanteenController::class, 'approveWithdrawal'])->name('canteen.withdrawals.approve');
        Route::post('canteen/withdrawals/{id}/reject', [AdminCanteenController::class, 'rejectWithdrawal'])->name('canteen.withdrawals.reject');

        // Canteen Vendor Users Management Routes
        Route::get('canteen/users', [AdminCanteenController::class, 'users'])->name('canteen.users');
        Route::post('canteen/users', [AdminCanteenController::class, 'storeUser'])->name('canteen.users.store');
        Route::put('canteen/users/{id}', [AdminCanteenController::class, 'updateUser'])->name('canteen.users.update');
        Route::delete('canteen/users/{id}', [AdminCanteenController::class, 'destroyUser'])->name('canteen.users.destroy');
        Route::post('canteen/users/{id}/toggle', [AdminCanteenController::class, 'toggleUserStatus'])->name('canteen.users.toggle');
    });

    // Settings & Profile Web
    Route::middleware('permission:view-settings')->group(function () {
        Route::get('profile-settings', [SettingController::class, 'profileEdit'])->name('profile-settings');
        Route::get('settings', [SettingController::class, 'edit'])->name('settings');
        Route::get('settings/index', [SettingController::class, 'edit'])->name('settings.index');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
        
        Route::get('email-settings', [EmailController::class, 'index'])->name('email.settings');
        Route::put('email-settings', [EmailController::class, 'update'])->name('email.update');
        Route::post('email-test', [EmailController::class, 'sendTest'])->name('email.test');
    });

    // Database Maintenance & Backup
    Route::middleware('role:super-admin|admin')->group(function () {
        Route::get('database-maintenance', [DatabaseMaintenanceController::class, 'index'])->name('database-maintenance.index');
        Route::post('database-maintenance/migrate', [DatabaseMaintenanceController::class, 'migrate'])->name('database-maintenance.migrate');
        Route::post('database-maintenance/sync-features', [DatabaseMaintenanceController::class, 'syncFeatures'])->name('database-maintenance.sync-features');
        Route::post('database-maintenance/backup', [DatabaseMaintenanceController::class, 'backup'])->name('database-maintenance.backup');
        Route::get('database-maintenance/download/{filename}', [DatabaseMaintenanceController::class, 'download'])->name('database-maintenance.download');
        Route::delete('database-maintenance/destroy/{filename}', [DatabaseMaintenanceController::class, 'destroy'])->name('database-maintenance.destroy');

        // Cryptographic License Key Generator Tool
        Route::get('license-generator', function () {
            return view('admin.license.generator');
        })->name('license-generator.index');
        Route::post('license-generator', function (\Illuminate\Http\Request $request) {
            $request->validate([
                'domain' => 'required|string',
                'client_name' => 'required|string',
            ]);
            $key = \App\Services\LicenseManager::generateLicense($request->domain, $request->client_name);
            if ($request->auto_install) {
                \App\Services\LicenseManager::saveLicense($key);
            }
            return back()->with([
                'generated_key' => $key,
                'gen_domain' => $request->domain,
                'gen_client' => $request->client_name,
                'gen_type' => 'LIFETIME',
                'auto_installed' => (bool) $request->auto_install,
            ]);
        })->name('license-generator.process');
    });
});

/*
|--------------------------------------------------------------------------
| Application License Activation Routes
|--------------------------------------------------------------------------
*/
Route::get('/license/activate', [\App\Http\Controllers\LicenseController::class, 'activate'])->name('license.activate');
Route::post('/license/activate', [\App\Http\Controllers\LicenseController::class, 'store'])->name('license.store');

/*
|--------------------------------------------------------------------------
| SPMB Routes
|--------------------------------------------------------------------------
*/
Route::prefix('spmb')->name('spmb.')->group(function () {
    // Public info route
    Route::get('info', [LandingController::class, 'spmbInfo'])->name('info');

    // Registration routes
    Route::get('/', [RegisterController::class, 'create'])->name('register');
    Route::post('/', [RegisterController::class, 'store'])->name('register.store');
    Route::get('success/{registration}', [RegisterController::class, 'success'])->name('success');
});

/*
|--------------------------------------------------------------------------
| Teacher Portal & Dashboard Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:guru|teacher|super-admin|admin'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Student Dashboard Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:student', 'verify.pin'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    
    // Profile & Account
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/account', [ProfileController::class, 'account'])->name('account');
    Route::put('/account', [ProfileController::class, 'updateAccount'])->name('account.update');
    Route::put('/account/pin', [ProfileController::class, 'updatePin'])->name('pin.update');
    
    // PIN Verification & Setup (Login / Session verification)
    Route::get('/pin/verify', [ProfileController::class, 'showPinVerifyForm'])->name('pin.verify');
    Route::post('/pin/verify', [ProfileController::class, 'verifyPin'])->name('pin.verify.submit');
    Route::get('/pin/set', [ProfileController::class, 'showPinSetForm'])->name('pin.set');
    Route::post('/pin/set', [ProfileController::class, 'setPin'])->name('pin.set.submit');
    
    // Announcements
    Route::get('/announcements', [StudentDashboardController::class, 'announcements'])->name('announcements');
    
    // Schedules
    Route::get('/schedules', [StudentScheduleController::class, 'index'])->name('schedules.index');
    
    // Assignments
    Route::get('/assignments', [StudentAssignmentController::class, 'index'])->name('assignments.index');
    Route::get('/assignments/{assignment}', [StudentAssignmentController::class, 'show'])->name('assignments.show');
    Route::post('/assignments/{assignment}/submit', [StudentAssignmentController::class, 'submit'])->name('assignments.submit');
    Route::get('/submissions/{submission}/download', [StudentAssignmentController::class, 'download'])->name('submissions.download');
    
    // Grades & Raport
    Route::get('/grades', [StudentGradeController::class, 'index'])->name('grades.index');
    Route::get('/raport/print', [StudentGradeController::class, 'printRaport'])->name('raport.print');
    
    // Materials
    Route::get('/materials', [StudentMaterialController::class, 'index'])->name('materials.index');
    Route::get('/materials/{material}/download', [StudentMaterialController::class, 'download'])->name('materials.download');

    // LMS Workspace
    Route::get('/lms', [\App\Http\Controllers\Student\StudentLmsController::class, 'index'])->name('lms.index');
    Route::get('/lms/learn/{chapterId}/{topicId?}', [\App\Http\Controllers\Student\StudentLmsController::class, 'learn'])->name('lms.learn');
    Route::post('/lms/complete-topic/{topic}', [\App\Http\Controllers\Student\StudentLmsController::class, 'completeTopic'])->name('lms.complete-topic');
    Route::post('/lms/submit-quiz/{quiz}', [\App\Http\Controllers\Student\StudentLmsController::class, 'submitQuiz'])->name('lms.submit-quiz');
    Route::get('/lms/gamification', [\App\Http\Controllers\Student\StudentLmsController::class, 'gamification'])->name('lms.gamification');
    Route::get('/lms/analytics', [\App\Http\Controllers\Student\StudentLmsController::class, 'analytics'])->name('lms.analytics');
    Route::get('/lms/live', [\App\Http\Controllers\Student\StudentLmsController::class, 'liveClass'])->name('lms.live');

    // CBT Online Exams
    Route::get('/exams', [\App\Http\Controllers\Student\ExamController::class, 'index'])->name('exams.index');
    Route::get('/exams/{id}', [\App\Http\Controllers\Student\ExamController::class, 'show'])->name('exams.show');
    Route::post('/exams/{id}/submit', [\App\Http\Controllers\Student\ExamController::class, 'submit'])->name('exams.submit');
    Route::put('/exams/{exam}/questions/{question}', [AdminExamController::class, 'updateQuestion'])->middleware('auth');
    Route::delete('/exams/{exam}/questions/{question}', [AdminExamController::class, 'destroyQuestion'])->middleware('auth');
    Route::post('/exams/{exam}/questions', [AdminExamController::class, 'storeQuestion'])->middleware('auth');

    // E-Library Digital
    Route::get('/library', [\App\Http\Controllers\Student\LibraryController::class, 'index'])->name('library.index');
    Route::get('/library/{id}', [\App\Http\Controllers\Student\LibraryController::class, 'show'])->name('library.show');

    // Savings
    Route::get('/savings', [\App\Http\Controllers\Student\StudentSavingsController::class, 'index'])->name('savings.index');
    Route::get('/savings/deposit', [\App\Http\Controllers\Student\StudentSavingsController::class, 'showDepositForm'])->name('savings.deposit.show');
    Route::post('/savings/deposit', [\App\Http\Controllers\Student\StudentSavingsController::class, 'checkoutDeposit'])->name('savings.deposit.checkout');
    Route::post('/savings/upload-proof', [\App\Http\Controllers\Student\StudentSavingsController::class, 'uploadManualProof'])->name('savings.deposit.upload-proof');
    Route::post('/savings/upload-proof-alias', [\App\Http\Controllers\Student\StudentSavingsController::class, 'uploadManualProof'])->name('savings.upload-proof');
    Route::post('/savings/deposit/cancel/{transaction}', [\App\Http\Controllers\Student\StudentSavingsController::class, 'cancelDeposit'])->name('savings.deposit.cancel');

    // Payments
    Route::get('/payments', [\App\Http\Controllers\Student\StudentPaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/pos/{paymentPost}', [\App\Http\Controllers\Student\StudentPaymentController::class, 'showPos'])->name('payments.show');
    Route::post('/payments/checkout', [\App\Http\Controllers\Student\StudentPaymentController::class, 'checkout'])->name('payments.checkout');
    Route::post('/payments/upload-proof', [\App\Http\Controllers\Student\StudentPaymentController::class, 'uploadManualProof'])->name('payments.upload-proof');

    // Digital Canteen Student Marketplace
    Route::get('/canteen', [StudentCanteenController::class, 'index'])->name('canteen.index');
    Route::get('/canteen/stall/{slug}', [StudentCanteenController::class, 'stallDetail'])->name('canteen.stall');
    Route::post('/canteen/checkout', [StudentCanteenController::class, 'checkout'])->name('canteen.checkout');
    Route::get('/canteen/orders', [StudentCanteenController::class, 'myOrders'])->name('canteen.my-orders');
    Route::get('/canteen/order/{id}', [StudentCanteenController::class, 'orderDetail'])->name('canteen.order');
    Route::post('/canteen/order/{id}/pay-savings', [StudentCanteenController::class, 'payWithSavings'])->name('canteen.pay-savings');
    Route::post('/canteen/process-qr', [StudentCanteenController::class, 'processQrScan'])->name('canteen.process-qr');
    // Permits (Izin & Sakit Siswa)
    Route::get('/permits', [\App\Http\Controllers\Student\StudentPermitController::class, 'index'])->name('permits.index');
    Route::get('/permits/create', [\App\Http\Controllers\Student\StudentPermitController::class, 'create'])->name('permits.create');
    Route::post('/permits', [\App\Http\Controllers\Student\StudentPermitController::class, 'store'])->name('permits.store');
    Route::delete('/permits/{permit}', [\App\Http\Controllers\Student\StudentPermitController::class, 'destroy'])->name('permits.destroy');
});

// Announcements for SPMB (calon-siswa)
Route::middleware(['auth', 'role:calon-siswa'])->prefix('spmb-dashboard')->name('spmb.dashboard.')->group(function () {
    Route::get('/announcements', [SpmbDashboardController::class, 'announcements'])->name('announcements');
});

/*
|--------------------------------------------------------------------------
| SPMB Dashboard (for registered candidates)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:calon-siswa'])->prefix('spmb-dashboard')->name('spmb.dashboard.')->group(function () {
    Route::get('/', [SpmbDashboardController::class, 'index'])->name('index');
    Route::post('/checkout', [SpmbDashboardController::class, 'checkout'])->name('checkout');
    Route::post('/upload-proof', [SpmbDashboardController::class, 'uploadManualProof'])->name('upload-proof');
    Route::post('/final-submit', [SpmbDashboardController::class, 'finalSubmit'])->name('final-submit');
    Route::get('/edit', [SpmbDashboardController::class, 'edit'])->name('edit');
    Route::put('/update', [SpmbDashboardController::class, 'update'])->name('update');
    Route::get('/account', [SpmbDashboardController::class, 'account'])->name('account');
    Route::put('/account', [SpmbDashboardController::class, 'updateAccount'])->name('account.update');
});

// SPMB Announcements
Route::middleware(['auth', 'role:calon-siswa'])->get('/spmb-dashboard/announcements', [SpmbDashboardController::class, 'announcements'])->name('spmb.dashboard.announcements');

/*
|--------------------------------------------------------------------------
| Canteen Vendor Mobile Portal (accessible by canteen vendor role & admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:kantin|canteen|admin|super-admin'])->prefix('canteen-vendor')->name('canteen.vendor.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Canteen\VendorCanteenController::class, 'dashboard'])->name('dashboard');
    Route::post('/toggle-status', [\App\Http\Controllers\Canteen\VendorCanteenController::class, 'toggleStatus'])->name('toggle-status');
    Route::get('/orders', [\App\Http\Controllers\Canteen\VendorCanteenController::class, 'orders'])->name('orders');
    Route::post('/orders/{id}/update-status', [\App\Http\Controllers\Canteen\VendorCanteenController::class, 'updateOrderStatus'])->name('orders.update-status');
    Route::get('/products', [\App\Http\Controllers\Canteen\VendorCanteenController::class, 'products'])->name('products');
    Route::post('/products', [\App\Http\Controllers\Canteen\VendorCanteenController::class, 'storeProduct'])->name('products.store');
    Route::put('/products/{id}', [\App\Http\Controllers\Canteen\VendorCanteenController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{id}', [\App\Http\Controllers\Canteen\VendorCanteenController::class, 'destroyProduct'])->name('products.destroy');
    Route::post('/products/{id}/toggle-availability', [\App\Http\Controllers\Canteen\VendorCanteenController::class, 'toggleProductAvailability'])->name('products.toggle-availability');
    // Vendor Category Management Routes
    Route::get('/categories', [\App\Http\Controllers\Canteen\VendorCanteenController::class, 'categories'])->name('categories');
    Route::post('/categories', [\App\Http\Controllers\Canteen\VendorCanteenController::class, 'storeCategory'])->name('categories.store');
    Route::put('/categories/{id}', [\App\Http\Controllers\Canteen\VendorCanteenController::class, 'updateCategory'])->name('categories.update');
    Route::post('/categories/{id}/toggle', [\App\Http\Controllers\Canteen\VendorCanteenController::class, 'toggleCategory'])->name('categories.toggle');
    Route::delete('/categories/{id}', [\App\Http\Controllers\Canteen\VendorCanteenController::class, 'destroyCategory'])->name('categories.destroy');

    // Vendor Stock Management Routes
    Route::get('/stock', [\App\Http\Controllers\Canteen\VendorCanteenController::class, 'stock'])->name('stock');
    Route::post('/stock/update-batch', [\App\Http\Controllers\Canteen\VendorCanteenController::class, 'updateStockBatch'])->name('stock.update-batch');
    Route::post('/stock/{id}/quick-update', [\App\Http\Controllers\Canteen\VendorCanteenController::class, 'quickUpdateStock'])->name('stock.quick-update');

    // Vendor Reports & Analytics Routes
    Route::get('/reports', [\App\Http\Controllers\Canteen\VendorCanteenController::class, 'reports'])->name('reports');
    Route::get('/reports/print', [\App\Http\Controllers\Canteen\VendorCanteenController::class, 'printReports'])->name('reports.print');
    Route::get('/reports/export-csv', [\App\Http\Controllers\Canteen\VendorCanteenController::class, 'exportReportsCsv'])->name('reports.export-csv');

    // Vendor POS (Point of Sale) Routes
    Route::get('/pos', [\App\Http\Controllers\Canteen\VendorCanteenController::class, 'pos'])->name('pos');
    Route::get('/pos/search-students', [\App\Http\Controllers\Canteen\VendorCanteenController::class, 'searchStudents'])->name('pos.search-students');
    Route::post('/pos/lookup-student', [\App\Http\Controllers\Canteen\VendorCanteenController::class, 'lookupStudentForPayment'])->name('pos.lookup-student');
    Route::post('/pos/checkout', [\App\Http\Controllers\Canteen\VendorCanteenController::class, 'posCheckout'])->name('pos.checkout');
    Route::get('/pos/order-status/{id}', [\App\Http\Controllers\Canteen\VendorCanteenController::class, 'checkOrderStatus'])->name('pos.order-status');

    Route::get('/profile', [\App\Http\Controllers\Canteen\VendorCanteenController::class, 'profile'])->name('profile');
    Route::post('/profile', [\App\Http\Controllers\Canteen\VendorCanteenController::class, 'updateProfile'])->name('profile.update');
    Route::get('/scan-qr', [\App\Http\Controllers\Canteen\VendorCanteenController::class, 'scanQr'])->name('scan-qr');
    Route::post('/verify-qr', [\App\Http\Controllers\Canteen\VendorCanteenController::class, 'verifyQrScan'])->name('verify-qr');

    // Vendor Balance & Withdrawal Routes
    Route::get('/finance', [\App\Http\Controllers\Canteen\VendorCanteenController::class, 'finance'])->name('finance');
    Route::post('/finance/bank-account', [\App\Http\Controllers\Canteen\VendorCanteenController::class, 'updateBankAccount'])->name('finance.bank-account');
    Route::post('/finance/withdraw', [\App\Http\Controllers\Canteen\VendorCanteenController::class, 'requestWithdrawal'])->name('finance.withdraw');
});


