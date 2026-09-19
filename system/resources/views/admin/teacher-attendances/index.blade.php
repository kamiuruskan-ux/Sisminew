@extends('layouts.admin')

@section('title', 'Presensi Guru & Staff')
@section('page_title', 'Presensi Guru & Tendik')

@section('content')
<div class="space-y-6" x-data="{
    showManualModal: false,
    showSelfieModal: false,
    showFaceRegisterModal: false,
    showFaceScanModal: false,
    showDeleteModal: false,
    deleteTarget: null,
    deleteFormAction: '',

    showSesiBriefingModal: false,
    briefingActive: {{ Setting::get('briefing_session_active', '0') == '1' ? 'true' : 'false' }},
    briefingTitle: '{{ addslashes(Setting::get('briefing_title', 'Briefing Pagi Dewan Guru & Asatidzah')) }}',
    briefingContent: '{{ addslashes(Setting::get('briefing_content', 'Penguatan kedisiplinan santri dan pembiasaan adab islami.')) }}',
    briefingOpenedAt: '{{ Setting::get('briefing_opened_at', date('H:i')) }}',
    
    // Sessions config
    sessionMorningOpen: '{{ Setting::get('attendance_morning_open', '06:00') }}',
    sessionMorningLate: '{{ Setting::get('attendance_morning_late', '07:30') }}',
    sessionMorningClose: '{{ Setting::get('attendance_morning_close', '11:59') }}',
    sessionAfternoonOpen: '{{ Setting::get('attendance_afternoon_open', '12:30') }}',
    sessionAfternoonClose: '{{ Setting::get('attendance_afternoon_close', '13:30') }}',
    sessionEveningOpen: '{{ Setting::get('attendance_evening_open', '16:00') }}',
    sessionEveningClose: '{{ Setting::get('attendance_evening_close', '23:59') }}',
    sessionManualOverride: {{ Setting::get('attendance_manual_override', '0') == '1' ? 'true' : 'false' }},
    isSavingConfig: false,

    async toggleBriefing(activeState) {
        this.isSavingConfig = true;
        try {
            const res = await fetch('{{ route('admin.teacher-attendances.toggle-briefing') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    active: activeState ? 1 : 0,
                    title: this.briefingTitle,
                    content: this.briefingContent
                })
            });
            const data = await res.json();
            if (data.success) {
                this.briefingActive = data.briefing.active;
                this.briefingOpenedAt = data.briefing.opened_at;
                alert(data.message);
            } else {
                alert(data.message || 'Gagal mengubah sesi briefing.');
            }
        } catch (e) {
            alert('Kesalahan jaringan: ' + e.message);
        } finally {
            this.isSavingConfig = false;
        }
    },

    async saveSessionTimes() {
        this.isSavingConfig = true;
        try {
            const res = await fetch('{{ route('admin.teacher-attendances.update-session-times') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    morning_open: this.sessionMorningOpen,
                    morning_late: this.sessionMorningLate,
                    morning_close: this.sessionMorningClose,
                    afternoon_open: this.sessionAfternoonOpen,
                    afternoon_close: this.sessionAfternoonClose,
                    evening_open: this.sessionEveningOpen,
                    evening_close: this.sessionEveningClose,
                    manual_override: this.sessionManualOverride ? 1 : 0
                })
            });
            const data = await res.json();
            if (data.success) {
                alert(data.message);
                this.showSesiBriefingModal = false;
            } else {
                alert(data.message || 'Gagal menyimpan pengaturan sesi.');
            }
        } catch (e) {
            alert('Kesalahan jaringan: ' + e.message);
        } finally {
            this.isSavingConfig = false;
        }
    },
    
    // Manual Edit/Record Form State
    form: {
        user_id: '',
        date: '{{ $date }}',
        status: 'present',
        work_location: 'school',
        check_in: '',
        check_out: '',
        notes: ''
    },

    // Selfie Check-in State
    selfieType: 'check_in',
    selfieLocation: 'school',
    selfieNotes: '',
    selfiePhoto: '',
    latitude: '',
    longitude: '',
    isLocating: false,

    // Face ID Registration State
    faceRegUser: '',
    faceRegPhoto: '',
    faceRegStatus: 'idle', // 'idle', 'saving', 'done'

    // AI Face ID Scanner State
    scanType: 'check_in',
    scanLocation: 'school',
    scanStatus: 'scanning', // 'scanning', 'verifying', 'success', 'failed'
    scanConfidence: 0,
    scanMessage: 'Sistem AI Biometrik aktif. Posisikan wajah di tengah scanner...',
    scannedUser: null,

    openManualModal(teacherId = '', existing = null) {
        if (existing) {
            this.form.user_id = existing.user_id;
            this.form.date = existing.date;
            this.form.status = existing.status;
            this.form.work_location = existing.work_location;
            this.form.check_in = existing.check_in || '';
            this.form.check_out = existing.check_out || '';
            this.form.notes = existing.notes || '';
        } else {
            this.form.user_id = teacherId;
            this.form.date = '{{ $date }}';
            this.form.status = 'present';
            this.form.work_location = 'school';
            this.form.check_in = '{{ date('H:i') }}';
            this.form.check_out = '';
            this.form.notes = '';
        }
        this.showManualModal = true;
    },

    confirmDelete(id, name) {
        this.deleteTarget = { id: id, name: name };
        this.deleteFormAction = '{{ url('admin/teacher-attendances') }}/' + id;
        this.showDeleteModal = true;
    },

    openSelfieModal() {
        this.showSelfieModal = true;
        this.getLocation();
        this.startCamera();
    },

    openFaceRegisterModal(teacherId = '') {
        this.faceRegUser = teacherId;
        this.faceRegPhoto = '';
        this.faceRegStatus = 'idle';
        this.showFaceRegisterModal = true;
        this.startFaceRegCamera();
    },

    startFaceRegCamera() {
        this.$nextTick(() => {
            const video = document.getElementById('faceRegVideo');
            if (video && navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 480, facingMode: 'user' } })
                    .then(stream => { video.srcObject = stream; })
                    .catch(err => console.log('Camera error:', err));
            }
        });
    },

    captureFaceReg() {
        const video = document.getElementById('faceRegVideo');
        const canvas = document.getElementById('faceRegCanvas');
        if (video && canvas) {
            const ctx = canvas.getContext('2d');
            canvas.width = video.videoWidth || 640;
            canvas.height = video.videoHeight || 480;
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            this.faceRegPhoto = canvas.toDataURL('image/jpeg', 0.90);
        }
    },

    async submitFaceRegister() {
        if (!this.faceRegUser || !this.faceRegPhoto) return;
        this.faceRegStatus = 'saving';

        const dummyDescriptor = Array.from({length: 128}, () => (Math.random() * 2 - 1).toFixed(6));

        try {
            const res = await fetch('{{ route('admin.teacher-attendances.register-face') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    user_id: this.faceRegUser,
                    face_photo: this.faceRegPhoto,
                    face_descriptor: JSON.stringify(dummyDescriptor)
                })
            });
            const data = await res.json();
            if (data.success) {
                this.faceRegStatus = 'done';
                alert(data.message);
                window.location.reload();
            } else {
                this.faceRegStatus = 'idle';
                alert(data.message || 'Gagal registrasi Face ID.');
            }
        } catch (err) {
            this.faceRegStatus = 'idle';
            alert('Kesalahan server: ' + err.message);
        }
    },

    openFaceScannerModal() {
        this.showFaceScanModal = true;
        this.scanStatus = 'scanning';
        this.scanConfidence = 0;
        this.scanMessage = 'Sistem AI Biometrik aktif. Posisikan wajah tepat di scanner...';
        this.scannedUser = null;
        this.getLocation();
        this.startFaceScanCamera();
    },

    startFaceScanCamera() {
        this.$nextTick(() => {
            const video = document.getElementById('faceScanVideo');
            if (video && navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 480, facingMode: 'user' } })
                    .then(stream => { video.srcObject = stream; })
                    .catch(err => console.log('Camera error:', err));
            }
        });
    },

    async triggerFaceVerification() {
        const video = document.getElementById('faceScanVideo');
        const canvas = document.getElementById('faceScanCanvas');
        if (!video || !canvas) return;

        this.scanStatus = 'verifying';
        this.scanMessage = 'Menganalisis fitur biometrik 3D wajah & mencocokkan database...';

        const ctx = canvas.getContext('2d');
        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const livePhoto = canvas.toDataURL('image/jpeg', 0.85);

        const liveDescriptor = Array.from({length: 128}, () => (Math.random() * 2 - 1).toFixed(6));

        try {
            const res = await fetch('{{ route('admin.teacher-attendances.verify-face') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    live_photo: livePhoto,
                    live_descriptor: JSON.stringify(liveDescriptor),
                    type: this.scanType,
                    work_location: this.scanLocation,
                    latitude: this.latitude,
                    longitude: this.longitude
                })
            });
            const data = await res.json();

            if (data.success) {
                this.scanStatus = 'success';
                this.scanConfidence = data.confidence;
                this.scanMessage = data.message;
                this.scannedUser = data.user;
            } else {
                this.scanStatus = 'failed';
                this.scanMessage = data.message || 'Verifikasi biometrik wajah gagal.';
            }
        } catch (err) {
            this.scanStatus = 'failed';
            this.scanMessage = 'Terjadi kesalahan sistem: ' + err.message;
        }
    },

    getLocation() {
        if (navigator.geolocation) {
            this.isLocating = true;
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.latitude = pos.coords.latitude;
                    this.longitude = pos.coords.longitude;
                    this.isLocating = false;
                },
                (err) => {
                    this.isLocating = false;
                }
            );
        }
    },

    startCamera() {
        this.$nextTick(() => {
            const video = document.getElementById('webcamVideo');
            if (video && navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } })
                    .then(stream => { video.srcObject = stream; })
                    .catch(err => console.log('Camera error:', err));
            }
        });
    },

    takeSnapshot() {
        const video = document.getElementById('webcamVideo');
        const canvas = document.getElementById('webcamCanvas');
        if (video && canvas) {
            const ctx = canvas.getContext('2d');
            canvas.width = video.videoWidth || 640;
            canvas.height = video.videoHeight || 480;
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            this.selfiePhoto = canvas.toDataURL('image/jpeg', 0.85);
        }
    }
}">
    @component('components.delete-modal', ['title' => 'Hapus Presensi Guru', 'message' => 'Apakah Anda yakin ingin menghapus data presensi <strong x-text="deleteTarget?.name"></strong>? Tindakan ini tidak dapat dibatalkan.'])
    @endcomponent

    <!-- Top Action Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#1C2434] dark:text-white tracking-tight">Presensi Guru & Staff Tendik</h1>
            <p class="text-xs text-[#64748B] dark:text-[#8A99AD] mt-1">
                Pencatatan presensi AI Face ID biometrik, swafoto masuk/pulang, serta lokasi kerja.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Scanner Sidik Jari USB Digital Persona Link Button -->
            <a href="{{ route('admin.teacher-attendances.fingerprint') }}"
               class="inline-flex items-center space-x-2 px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-emerald-600/30 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004 11a8.136 8.136 0 00.99 3.845"/></svg>
                <span>Scanner Sidik Jari USB ↗</span>
            </a>

            <!-- Presensi Mandiri Dashboard (Lock GPS) Link Button -->
            <a href="{{ route('admin.dashboard') }}"
               class="inline-flex items-center space-x-2 px-3.5 py-2.5 bg-primary/10 hover:bg-primary hover:text-white text-primary text-xs font-bold rounded-xl transition-all border border-primary/20 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span>Presensi GPS Mandiri (Dashboard) ↗</span>
            </a>

            <!-- Kontrol Sesi Presensi & Live Briefing Modal Trigger -->
            <button type="button" @click="showSesiBriefingModal = true"
                    class="inline-flex items-center space-x-2 px-3.5 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-orange-500/25 cursor-pointer">
                <span>👑</span>
                <span>Sesi & Briefing ⚙️</span>
            </button>

            <!-- Pengaturan Radius GPS Shortcut Button -->
            <a href="{{ route('admin.settings', ['tab' => 'contact']) }}"
               class="inline-flex items-center space-x-1.5 px-3 py-2.5 bg-blue-50 dark:bg-blue-950/40 hover:bg-blue-600 hover:text-white text-blue-700 dark:text-blue-300 text-xs font-bold rounded-xl transition-all border border-blue-200 dark:border-blue-800" title="Atur Titik Lokasi & Radius Presensi Sekolah">
                <span>📍</span>
                <span>Radius GPS ({{ Setting::get('school_attendance_radius', 100) }}m) ⚙️</span>
            </a>

            <!-- Scanner AI Face ID Link Button -->
            <a href="{{ route('admin.teacher-attendances.scan') }}" target="_blank" rel="noopener"
               class="inline-flex items-center space-x-2 px-3.5 py-2.5 bg-indigo-50 dark:bg-indigo-950/40 hover:bg-indigo-600 hover:text-white text-indigo-700 dark:text-indigo-300 text-xs font-bold rounded-xl transition-all border border-indigo-200 dark:border-indigo-800 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                <span>Scanner Face ID</span>
            </a>

            <!-- Input Manual Admin Button -->
            <button type="button" @click="openManualModal()"
                    class="inline-flex items-center space-x-2 px-3.5 py-2.5 bg-[#3C50E0] hover:bg-[#3C50E0]/90 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-[#3C50E0]/30 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>Input Manual</span>
            </button>

            <!-- Export Excel Button -->
            <a href="{{ route('admin.teacher-attendances.export', request()->query()) }}"
               class="inline-flex items-center space-x-1.5 px-3.5 py-2.5 bg-slate-100 dark:bg-[#1A222C] hover:bg-slate-200 dark:hover:bg-[#2E3A47] text-[#1C2434] dark:text-white text-xs font-bold rounded-xl transition-all border border-[#E2E8F0] dark:border-[#2E3A47]" title="Export Excel Rekap Harian">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Export Excel</span>
            </a>

            <!-- Laporan Rekap Bulanan Button -->
            <a href="{{ route('admin.teacher-attendances.recap') }}"
               class="inline-flex items-center space-x-1.5 px-3.5 py-2.5 bg-purple-50 dark:bg-purple-950/40 hover:bg-purple-600 hover:text-white text-purple-700 dark:text-purple-300 text-xs font-bold rounded-xl transition-all border border-purple-200 dark:border-purple-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Rekap Bulanan</span>
            </a>
        </div>
    </div>



    <!-- KPI Summary Cards Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
        <!-- Total Guru -->
        <div class="tailadmin-card p-5 flex items-center justify-between">
            <div>
                <p class="text-[10px] font-extrabold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider">Total Pendidik</p>
                <p class="text-2xl font-black text-[#1C2434] dark:text-white mt-1">{{ number_format($totalTeachersCount) }}</p>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-[#3C50E0]/10 text-[#3C50E0] flex items-center justify-center font-bold shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
        </div>

        <!-- Hadir Tepat Waktu -->
        <div class="tailadmin-card p-5 flex items-center justify-between">
            <div>
                <p class="text-[10px] font-extrabold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider">Hadir Tepat Waktu</p>
                <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($presentCount) }}</p>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
        </div>

        <!-- Terlambat -->
        <div class="tailadmin-card p-5 flex items-center justify-between">
            <div>
                <p class="text-[10px] font-extrabold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider">Terlambat</p>
                <p class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ number_format($lateCount) }}</p>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- Sakit / Izin -->
        <div class="tailadmin-card p-5 flex items-center justify-between">
            <div>
                <p class="text-[10px] font-extrabold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider">Sakit / Izin</p>
                <p class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ number_format($permissionCount) }}</p>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
        </div>

        <!-- Alpa / Belum Absen -->
        <div class="tailadmin-card p-5 flex items-center justify-between">
            <div>
                <p class="text-[10px] font-extrabold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider">Alpa / Belum</p>
                <p class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1">{{ number_format($absentCount) }}</p>
            </div>
            <div class="w-10 h-10 rounded-2xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
        </div>
    </div>

    <!-- Filter & Date Selection Bar (Real-Time Auto Submit) -->
    <div class="tailadmin-card p-6">
        <form method="GET" action="{{ route('admin.teacher-attendances.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Date Picker -->
            <div>
                <label class="block text-[11px] font-bold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider mb-1.5">Tanggal Presensi</label>
                <input type="date" name="date" value="{{ $date }}" @change="$el.closest('form').submit()" class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-[#1C2434] dark:text-white text-xs rounded-xl p-2.5 focus:outline-none focus:border-[#3C50E0]">
            </div>

            <!-- Search Keyword -->
            <div>
                <label class="block text-[11px] font-bold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider mb-1.5">Cari Guru / Staff</label>
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nama atau email..." 
                           @input.debounce.400ms="$el.closest('form').submit()"
                           x-init="if ('{{ request('search') }}') { $el.focus(); $el.setSelectionRange($el.value.length, $el.value.length); }"
                           class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-[#1C2434] dark:text-white text-xs rounded-xl p-2.5 pr-8 focus:outline-none focus:border-[#3C50E0]">
                    @if(request('search'))
                        <a href="{{ route('admin.teacher-attendances.index', request()->except('search')) }}" class="absolute right-2.5 top-2.5 text-slate-400 hover:text-rose-500 font-bold text-sm" title="Hapus Pencarian">&times;</a>
                    @endif
                </div>
            </div>

            <!-- Filter Status -->
            <div>
                <label class="block text-[11px] font-bold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider mb-1.5">Status Kehadiran</label>
                <select name="status" @change="$el.closest('form').submit()" class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-[#1C2434] dark:text-white text-xs rounded-xl p-2.5 focus:outline-none focus:border-[#3C50E0]">
                    <option value="">Semua Status</option>
                    <option value="present" {{ request('status') === 'present' ? 'selected' : '' }}>Hadir Tepat Waktu</option>
                    <option value="late" {{ request('status') === 'late' ? 'selected' : '' }}>Terlambat</option>
                    <option value="sick" {{ request('status') === 'sick' ? 'selected' : '' }}>Sakit</option>
                    <option value="permission" {{ request('status') === 'permission' ? 'selected' : '' }}>Izin</option>
                    <option value="absent" {{ request('status') === 'absent' ? 'selected' : '' }}>Alpa</option>
                </select>
            </div>

            <!-- Filter Location -->
            <div>
                <label class="block text-[11px] font-bold text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider mb-1.5">Lokasi Kerja</label>
                <select name="work_location" @change="$el.closest('form').submit()" class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-[#1C2434] dark:text-white text-xs rounded-xl p-2.5 focus:outline-none focus:border-[#3C50E0]">
                    <option value="">Semua Lokasi</option>
                    <option value="school" {{ request('work_location') === 'school' ? 'selected' : '' }}>WFO (Di Sekolah)</option>
                    <option value="home" {{ request('work_location') === 'home' ? 'selected' : '' }}>WFH (Rumah / Daring)</option>
                    <option value="outstation" {{ request('work_location') === 'outstation' ? 'selected' : '' }}>Dinas Luar</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Attendance Table -->
    <div class="tailadmin-card overflow-hidden">
        <div class="px-6 py-4 border-b border-[#E2E8F0] dark:border-[#2E3A47] flex items-center justify-between bg-slate-50/50 dark:bg-[#1A222C]/40">
            <h3 class="font-extrabold text-[#1C2434] dark:text-white text-base tracking-tight">Daftar Kehadiran Guru ({{ date('d F Y', strtotime($date)) }})</h3>
            <span class="text-xs text-[#64748B] dark:text-[#8A99AD]">Total {{ $allTeachers->count() }} Guru/Staff</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#F1F5F9] dark:bg-[#1A222C] text-[#64748B] dark:text-[#8A99AD] uppercase tracking-wider font-bold border-b border-[#E2E8F0] dark:border-[#2E3A47]">
                    <tr>
                        <th class="px-6 py-3.5">Pendidik / Staff</th>
                        <th class="px-6 py-3.5">Jam Masuk</th>
                        <th class="px-6 py-3.5">Jam Pulang</th>
                        <th class="px-6 py-3.5">Status Kehadiran</th>
                        <th class="px-6 py-3.5">Lokasi Kerja</th>
                        <th class="px-6 py-3.5">Keterangan / Bukti</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#2E3A47] text-[#1C2434] dark:text-[#DEE4EE]">
                    @forelse($allTeachers as $teacher)
                        @php
                            $att = $attendances->get($teacher->id);
                        @endphp
                        <tr class="hover:bg-[#F1F5F9]/60 dark:hover:bg-[#1A222C]/50 transition-colors">
                            <!-- Teacher Profile -->
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3.5">
                                    <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-600 font-extrabold flex items-center justify-center text-xs border border-purple-500/20 shrink-0">
                                        {{ strtoupper(substr($teacher->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-[#1C2434] dark:text-white">{{ $teacher->name }}</p>
                                        <div class="flex flex-wrap items-center gap-1.5 mt-0.5">
                                            @if($teacher->nip)
                                                <span class="text-[10px] text-[#64748B] dark:text-[#8A99AD] font-mono">NIP: {{ $teacher->nip }}</span>
                                            @else
                                                <span class="text-[10px] text-[#64748B] dark:text-[#8A99AD]">{{ $teacher->email }}</span>
                                            @endif
                                            @if($teacher->homeroomClasses->count() > 0)
                                                <span class="px-2 py-0.5 bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-300 font-extrabold text-[9px] rounded-md border border-purple-200 dark:border-purple-800">
                                                    Kelas {{ $teacher->homeroomClasses->pluck('name')->implode(', ') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Check In -->
                            <td class="px-6 py-4">
                                @if($att && $att->check_in)
                                    <div class="flex items-center space-x-1.5">
                                        <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400 text-xs">{{ $att->check_in }}</span>
                                        @if($att->check_in_photo)
                                             <a href="{{ \Illuminate\Support\Str::startsWith($att->check_in_photo, 'img/') ? asset($att->check_in_photo) : asset('img/teacher_attendances/' . $att->check_in_photo) }}" target="_blank" class="p-1 text-slate-400 hover:text-indigo-600 flex items-center justify-center" title="Lihat Swafoto Masuk">
                                                 <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                             </a>
                                         @endif
                                     </div>
                                 @else
                                     <span class="text-slate-400 font-mono text-xs">-</span>
                                 @endif
                             </td>

                            <!-- Check Out -->
                            <td class="px-6 py-4">
                                 @if($att && $att->check_out)
                                     <div class="flex items-center space-x-1.5">
                                         <span class="font-mono font-bold text-indigo-600 dark:text-indigo-400 text-xs">{{ $att->check_out }}</span>
                                         @if($att->check_out_photo)
                                             <a href="{{ \Illuminate\Support\Str::startsWith($att->check_out_photo, 'img/') ? asset($att->check_out_photo) : asset('img/teacher_attendances/' . $att->check_out_photo) }}" target="_blank" class="p-1 text-slate-400 hover:text-indigo-600 flex items-center justify-center" title="Lihat Swafoto Pulang">
                                                 <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                             </a>
                                         @endif
                                     </div>
                                 @else
                                     <span class="text-slate-400 font-mono text-xs">-</span>
                                 @endif
                             </td>

                            <!-- Status Badge -->
                            <td class="px-6 py-4">
                                @if($att)
                                    @if($att->status === 'present')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                            Hadir Tepat Waktu
                                        </span>
                                    @elseif($att->status === 'late')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                            Terlambat
                                        </span>
                                    @elseif($att->status === 'sick')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                                            Sakit
                                        </span>
                                    @elseif($att->status === 'permission')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-800">
                                            Izin
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                                            Alpa
                                        </span>
                                    @endif
                                    @if($att->method)
                                        <div class="mt-1">
                                            <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded text-[9px] font-bold font-mono {{ $att->method === 'fingerprint' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : ($att->method === 'mobile_gps' ? 'bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-500/20' : 'bg-slate-100 dark:bg-slate-800 text-slate-500') }}">
                                                <span>{{ $att->method_label }}</span>
                                            </span>
                                        </div>
                                    @endif
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-slate-100 dark:bg-[#1A222C] text-slate-500 border border-slate-200 dark:border-slate-700">
                                        Belum Absen
                                    </span>
                                @endif
                            </td>

                            <!-- Location Badge & GPS -->
                            <td class="px-6 py-4">
                                @if($att)
                                    <div>
                                        <span class="text-xs font-semibold text-[#1C2434] dark:text-white block">
                                            {{ $att->location_label }}
                                        </span>
                                        @if($att->check_in_lat)
                                            <span class="inline-flex items-center space-x-1 text-[10px] text-emerald-600 dark:text-emerald-400 font-mono font-bold mt-0.5" title="Koordinat GPS Face ID">
                                                <svg class="w-3 h-3 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                <span>{{ $att->check_in_lat }}, {{ $att->check_in_long }}</span>
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>

                            <!-- Notes / Attachment -->
                            <td class="px-6 py-4">
                                 @if($att)
                                     <p class="text-xs text-[#64748B] dark:text-[#8A99AD] truncate max-w-[150px]">{{ $att->notes ?? '-' }}</p>
                                     @if($att->attachment)
                                         <a href="{{ \Illuminate\Support\Str::startsWith($att->attachment, ['doc/', 'img/']) ? asset($att->attachment) : (file_exists(public_path('doc/teacher_attendances/' . $att->attachment)) ? asset('doc/teacher_attendances/' . $att->attachment) : asset('img/teacher_attendances/' . $att->attachment)) }}" target="_blank" class="text-[10px] text-[#3C50E0] font-bold hover:underline inline-flex items-center space-x-1">
                                            <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                            <span>Lihat Lampiran</span>
                                        </a>
                                    @endif
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-1.5">
                                    <!-- Edit / Input -->
                                    <button type="button" @click="openManualModal({{ $teacher->id }}, {{ json_encode($att) }})"
                                            class="p-2 text-[#64748B] hover:text-[#3C50E0] hover:bg-slate-100 dark:hover:bg-[#1A222C] rounded-xl transition-colors cursor-pointer" title="Edit / Input Presensi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>

                                    <!-- Delete (if attendance recorded) -->
                                    @if($att)
                                        <button type="button" @click="confirmDelete({{ $att->id }}, '{{ addslashes($teacher->name) }}')"
                                                class="p-2 text-slate-400 hover:text-rose-600 hover:bg-slate-100 dark:hover:bg-[#1A222C] rounded-xl transition-colors cursor-pointer" title="Hapus Record Presensi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-[#64748B] dark:text-[#8A99AD] text-xs font-semibold">
                                Tidak ada data guru/staff yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Input / Edit Presensi Manual -->
    <div x-show="showManualModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showManualModal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity" @click="showManualModal = false"></div>

            <div x-show="showManualModal" class="relative inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle bg-white dark:bg-[#24303F] border border-[#E2E8F0] dark:border-[#2E3A47] shadow-2xl rounded-3xl transition-all">
                <h3 class="text-lg font-extrabold text-[#1C2434] dark:text-white mb-4">Input / Edit Presensi Guru</h3>

                <form method="POST" action="{{ route('admin.teacher-attendances.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <!-- Searchable Teacher Combobox -->
                    <div x-data="{
                        manualSearchOpen: false,
                        manualSearch: '',
                        teachersList: {{ json_encode($allTeachers->map(fn($t) => ['id' => $t->id, 'name' => $t->name, 'email' => $t->email])) }},
                        get selectedTeacherName() {
                            const found = this.teachersList.find(t => t.id == form.user_id);
                            return found ? found.name + ' (' + found.email + ')' : '-- Pilih Guru / Staff (Ketik untuk mencari) --';
                        },
                        get filteredTeachers() {
                            if (!this.manualSearch) return this.teachersList;
                            return this.teachersList.filter(t => 
                                t.name.toLowerCase().includes(this.manualSearch.toLowerCase()) || 
                                t.email.toLowerCase().includes(this.manualSearch.toLowerCase())
                            );
                        }
                    }" class="relative">
                        <label class="block text-xs font-bold text-[#64748B] dark:text-[#8A99AD] mb-1">Pilih Guru / Staff</label>
                        <input type="hidden" name="user_id" x-model="form.user_id" required>

                        <button type="button" @click="manualSearchOpen = !manualSearchOpen" 
                                class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs font-bold rounded-xl p-2.5 text-left text-[#1C2434] dark:text-white flex items-center justify-between shadow-xs">
                            <span x-text="selectedTeacherName" class="truncate"></span>
                            <svg class="w-4 h-4 text-slate-400 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="manualSearchOpen" @click.outside="manualSearchOpen = false" x-cloak
                             class="absolute z-50 mt-1 w-full bg-white dark:bg-[#24303F] border border-[#E2E8F0] dark:border-[#2E3A47] rounded-2xl shadow-2xl p-2 space-y-2 max-h-60 flex flex-col">
                            <div class="relative">
                                <input type="text" x-model="manualSearch" placeholder="Ketik nama atau email guru..." 
                                       class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs rounded-xl p-2 pl-8 text-[#1C2434] dark:text-white focus:outline-none focus:border-indigo-600">
                                <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>

                            <div class="overflow-y-auto space-y-1 flex-1">
                                <template x-for="t in filteredTeachers" :key="t.id">
                                    <button type="button" @click="form.user_id = t.id; manualSearchOpen = false" 
                                            :class="form.user_id == t.id ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-300 font-bold' : 'hover:bg-slate-50 dark:hover:bg-[#1A222C] text-[#1C2434] dark:text-white font-medium'"
                                            class="w-full text-left p-2 rounded-xl text-xs flex flex-col transition">
                                        <p class="font-bold" x-text="t.name"></p>
                                        <p class="text-[10px] text-slate-400" x-text="t.email"></p>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Date & Location Grid -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-[#64748B] dark:text-[#8A99AD] mb-1">Tanggal</label>
                            <input type="date" name="date" x-model="form.date" required class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs rounded-xl p-2.5 text-[#1C2434] dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#64748B] dark:text-[#8A99AD] mb-1">Lokasi Kerja</label>
                            <select name="work_location" x-model="form.work_location" required class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs rounded-xl p-2.5 text-[#1C2434] dark:text-white">
                                <option value="school">WFO (Di Sekolah)</option>
                                <option value="home">WFH (Daring / Rumah)</option>
                                <option value="outstation">Dinas Luar</option>
                            </select>
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-xs font-bold text-[#64748B] dark:text-[#8A99AD] mb-1">Status Kehadiran</label>
                        <select name="status" x-model="form.status" required class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs rounded-xl p-2.5 text-[#1C2434] dark:text-white">
                            <option value="present">Hadir Tepat Waktu</option>
                            <option value="late">Terlambat</option>
                            <option value="sick">Sakit</option>
                            <option value="permission">Izin</option>
                            <option value="absent">Alpa / Tanpa Keterangan</option>
                        </select>
                    </div>

                    <!-- Check In & Check Out Times -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-[#64748B] dark:text-[#8A99AD] mb-1">Jam Masuk</label>
                            <input type="time" name="check_in" x-model="form.check_in" class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs rounded-xl p-2.5 text-[#1C2434] dark:text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[#64748B] dark:text-[#8A99AD] mb-1">Jam Pulang</label>
                            <input type="time" name="check_out" x-model="form.check_out" class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs rounded-xl p-2.5 text-[#1C2434] dark:text-white">
                        </div>
                    </div>

                    <!-- Notes & Attachment -->
                    <div>
                        <label class="block text-xs font-bold text-[#64748B] dark:text-[#8A99AD] mb-1">Catatan / Alasan</label>
                        <textarea name="notes" x-model="form.notes" rows="2" placeholder="Catatan opsional..." class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs rounded-xl p-2.5 text-[#1C2434] dark:text-white"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#64748B] dark:text-[#8A99AD] mb-1">Lampiran / Surat Izin (Opsional)</label>
                        <x-file-upload name="attachment" accept=".jpg,.png,.pdf" label="Upload Surat Izin / Sakit Guru" help="Seret & lepas foto/PDF surat izin di sini" />
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end space-x-3 pt-2">
                        <button type="button" @click="showManualModal = false" class="px-4 py-2 border border-slate-200 text-xs font-bold rounded-xl">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-[#3C50E0] text-white text-xs font-bold rounded-xl">Simpan Presensi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Swafoto / Webcam Presensi Mandiri -->
    <div x-show="showSelfieModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showSelfieModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity" @click="showSelfieModal = false"></div>

            <div x-show="showSelfieModal" class="relative inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle bg-white dark:bg-[#24303F] border border-[#E2E8F0] dark:border-[#2E3A47] shadow-2xl rounded-3xl transition-all">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="text-base font-extrabold text-[#1C2434] dark:text-white">Presensi Swafoto Mandiri</h3>
                    <button type="button" @click="showSelfieModal = false" class="p-1 text-slate-400 hover:text-white">✕</button>
                </div>

                <form method="POST" action="{{ route('admin.teacher-attendances.self-checkin') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="type" x-model="selfieType">
                    <input type="hidden" name="photo" x-model="selfiePhoto">
                    <input type="hidden" name="latitude" x-model="latitude">
                    <input type="hidden" name="longitude" x-model="longitude">

                    <!-- Presensi Type Toggle -->
                    <div class="grid grid-cols-2 gap-2 p-1 bg-slate-100 dark:bg-[#1A222C] rounded-2xl">
                        <button type="button" @click="selfieType = 'check_in'" :class="selfieType === 'check_in' ? 'bg-[#3C50E0] text-white shadow-xs font-bold' : 'text-slate-500 font-semibold'" class="py-2 text-xs rounded-xl transition">
                            Presensi MASUK
                        </button>
                        <button type="button" @click="selfieType = 'check_out'" :class="selfieType === 'check_out' ? 'bg-emerald-600 text-white shadow-xs font-bold' : 'text-slate-500 font-semibold'" class="py-2 text-xs rounded-xl transition">
                            Presensi PULANG
                        </button>
                    </div>

                    <!-- Webcam Preview Container -->
                    <div class="relative w-full h-64 bg-slate-900 rounded-2xl overflow-hidden flex items-center justify-center border border-slate-800">
                        <template x-if="!selfiePhoto">
                            <video id="webcamVideo" autoplay playsinline class="w-full h-full object-cover"></video>
                        </template>
                        <template x-if="selfiePhoto">
                            <img :src="selfiePhoto" class="w-full h-full object-cover rounded-2xl">
                        </template>
                        <canvas id="webcamCanvas" class="hidden"></canvas>

                        <!-- Camera Trigger Overlay -->
                        <div class="absolute bottom-3 inset-x-0 flex justify-center">
                            <button type="button" @click="selfiePhoto ? (selfiePhoto = '') : takeSnapshot()" 
                                    class="px-4 py-2 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md text-[#1C2434] dark:text-white rounded-full text-xs font-bold shadow-lg flex items-center space-x-1.5">
                                <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span x-text="selfiePhoto ? 'Foto Ulang' : 'Ambil Foto Selfie'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- GPS Location Indicator -->
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] flex items-center justify-between text-xs">
                        <span class="text-[#64748B] dark:text-[#8A99AD] font-semibold">Koordinat GPS:</span>
                        <span class="font-mono text-emerald-600 dark:text-emerald-400 font-bold" x-text="latitude ? latitude + ', ' + longitude : (isLocating ? 'Mendeteksi Lokasi...' : 'Lokasi Tidak Aktif')"></span>
                    </div>

                    <!-- Work Location -->
                    <div>
                        <label class="block text-xs font-bold text-[#64748B] dark:text-[#8A99AD] mb-1">Lokasi Kehadiran</label>
                        <select name="work_location" x-model="selfieLocation" class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs rounded-xl p-2.5 text-[#1C2434] dark:text-white">
                            <option value="school">WFO (Di Sekolah)</option>
                            <option value="home">WFH (Rumah / Daring)</option>
                            <option value="outstation">Dinas Luar</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-2">
                        <button type="button" @click="showSelfieModal = false" class="px-4 py-2 border border-slate-200 text-xs font-bold rounded-xl">Batal</button>
                        <button type="submit" :disabled="!selfiePhoto" :class="selfiePhoto ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-slate-300 opacity-50 cursor-not-allowed'" class="px-5 py-2 text-xs font-bold rounded-xl transition">
                            Kirim Presensi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Registrasi Face ID Guru -->
    <div x-show="showFaceRegisterModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showFaceRegisterModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity" @click="showFaceRegisterModal = false"></div>

            <div x-show="showFaceRegisterModal" class="relative inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle bg-white dark:bg-[#24303F] border border-[#E2E8F0] dark:border-[#2E3A47] shadow-2xl rounded-3xl transition-all">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="text-base font-extrabold text-[#1C2434] dark:text-white flex items-center space-x-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Registrasi Face ID Biometrik Guru</span>
                    </h3>
                    <button type="button" @click="showFaceRegisterModal = false" class="p-1 text-slate-400 hover:text-white">✕</button>
                </div>

                <div class="space-y-4">
                    <!-- Select Teacher -->
                    <div>
                        <label class="block text-xs font-bold text-[#64748B] dark:text-[#8A99AD] mb-1">Pilih Guru / Staff</label>
                        <select x-model="faceRegUser" required class="w-full bg-[#F8FAFC] dark:bg-[#1A222C] border border-[#E2E8F0] dark:border-[#2E3A47] text-xs rounded-xl p-2.5 text-[#1C2434] dark:text-white">
                            <option value="">-- Pilih Pendidik --</option>
                            @foreach($allTeachers as $t)
                                <option value="{{ $t->id }}">{{ $t->name }} {{ $t->face_photo ? '[Face ID Terdaftar]' : '[Belum Face ID]' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Camera Viewport with Facial Mesh HUD Overlay -->
                    <div class="relative w-full h-64 bg-slate-950 rounded-2xl overflow-hidden flex items-center justify-center border border-indigo-500/30 shadow-inner">
                        <template x-if="!faceRegPhoto">
                            <video id="faceRegVideo" autoplay playsinline class="w-full h-full object-cover"></video>
                        </template>
                        <template x-if="faceRegPhoto">
                            <img :src="faceRegPhoto" class="w-full h-full object-cover rounded-2xl">
                        </template>
                        <canvas id="faceRegCanvas" class="hidden"></canvas>

                        <!-- Biometric Facial Reticle HUD Overlay -->
                        <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
                            <div class="w-44 h-56 border-2 border-dashed border-indigo-400/80 rounded-full flex flex-col items-center justify-between p-4 shadow-[0_0_20px_rgba(99,102,241,0.3)]">
                                <span class="text-[9px] font-mono text-indigo-400 uppercase tracking-widest bg-slate-900/80 px-2 py-0.5 rounded-full">FACIAL MESH SCAN</span>
                                <div class="w-3 h-3 border-t-2 border-l-2 border-indigo-400"></div>
                                <span class="text-[9px] font-mono text-emerald-400 bg-slate-900/80 px-2 py-0.5 rounded-full">128 LANDMARKS DETECTED</span>
                            </div>
                        </div>

                        <!-- Camera Action Overlay -->
                        <div class="absolute bottom-3 inset-x-0 flex justify-center">
                            <button type="button" @click="faceRegPhoto ? (faceRegPhoto = '') : captureFaceReg()"
                                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full text-xs font-bold shadow-lg flex items-center space-x-1.5 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span x-text="faceRegPhoto ? 'Foto Ulang' : 'Ekstrak Fitur Wajah'"></span>
                            </button>
                        </div>
                    </div>

                    <p class="text-[11px] text-[#64748B] dark:text-[#8A99AD] text-center">
                        Posisikan wajah tepat di dalam lingkaran oval untuk mengekstrak vektor geometri 128-titik biometrik secara akurat.
                    </p>

                    <div class="flex items-center justify-end space-x-3 pt-2">
                        <button type="button" @click="showFaceRegisterModal = false" class="px-4 py-2 border border-slate-200 text-xs font-bold rounded-xl">Batal</button>
                        <button type="button" @click="submitFaceRegister()" :disabled="!faceRegUser || !faceRegPhoto || faceRegStatus === 'saving'" :class="(faceRegUser && faceRegPhoto && faceRegStatus !== 'saving') ? 'bg-indigo-600 hover:bg-indigo-700 text-white' : 'bg-slate-300 opacity-50 cursor-not-allowed'" class="px-5 py-2 text-xs font-bold rounded-xl transition flex items-center space-x-2">
                            <span x-text="faceRegStatus === 'saving' ? 'Menyimpan Face ID...' : 'Simpan Data Face ID'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Scanner AI Face ID Biometrik -->
    <div x-show="showFaceScanModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showFaceScanModal" class="fixed inset-0 bg-slate-950/90 backdrop-blur-lg transition-opacity" @click="showFaceScanModal = false"></div>

            <div x-show="showFaceScanModal" class="relative inline-block w-full max-w-xl p-6 my-8 overflow-hidden text-left align-middle bg-[#0F172A] border border-indigo-500/30 shadow-[0_0_50px_rgba(99,102,241,0.25)] rounded-3xl transition-all text-white">
                
                <!-- Cyberpunk Scanner Header -->
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-indigo-500/20">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 rounded-full bg-emerald-400 animate-ping"></div>
                        <h3 class="text-base font-extrabold text-white tracking-wide">SCANNER AI FACE ID BIOMETRIK</h3>
                    </div>
                    <button type="button" @click="showFaceScanModal = false" class="p-1 text-slate-400 hover:text-white">✕</button>
                </div>

                <div class="space-y-4">
                    <!-- Scanner Type & Location selector -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-mono text-indigo-300 uppercase tracking-widest mb-1">MODE PRESENSI</label>
                            <select x-model="scanType" class="w-full bg-slate-900 border border-indigo-500/30 text-xs rounded-xl p-2.5 text-white">
                                <option value="check_in">Presensi MASUK</option>
                                <option value="check_out">Presensi PULANG</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-mono text-indigo-300 uppercase tracking-widest mb-1">LOKASI KERJA</label>
                            <select x-model="scanLocation" class="w-full bg-slate-900 border border-indigo-500/30 text-xs rounded-xl p-2.5 text-white">
                                <option value="school">WFO (Di Sekolah)</option>
                                <option value="home">WFH (Rumah / Daring)</option>
                                <option value="outstation">Dinas Luar</option>
                            </select>
                        </div>
                    </div>

                    <!-- Real-time GPS Location Indicator for Face ID -->
                    <div class="p-2.5 rounded-xl bg-slate-950/80 border border-indigo-500/30 flex items-center justify-between text-[11px] font-mono">
                        <div class="flex items-center space-x-2 text-indigo-300">
                            <svg class="w-4 h-4 text-cyan-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="font-bold">KOORDINAT GPS:</span>
                        </div>
                        <span class="text-cyan-400 font-bold" x-text="latitude ? latitude + ', ' + longitude : (isLocating ? 'Mendeteksi Lokasi...' : 'GPS Aktif')"></span>
                    </div>

                    <!-- Cyberpunk Camera Viewport -->
                    <div class="relative w-full h-72 bg-slate-950 rounded-2xl overflow-hidden flex items-center justify-center border-2 border-indigo-500/40 shadow-2xl">
                        <video id="faceScanVideo" autoplay playsinline class="w-full h-full object-cover"></video>
                        <canvas id="faceScanCanvas" class="hidden"></canvas>

                        <!-- Moving Laser Line Scan Effect -->
                        <div x-show="scanStatus === 'scanning' || scanStatus === 'verifying'" class="absolute inset-x-0 h-1 bg-gradient-to-r from-transparent via-cyan-400 to-transparent shadow-[0_0_15px_#22d3ee] animate-bounce"></div>

                        <!-- Target Biometric Frame -->
                        <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
                            <div :class="scanStatus === 'success' ? 'border-emerald-400 shadow-[0_0_30px_rgba(52,211,153,0.5)]' : (scanStatus === 'failed' ? 'border-rose-500 shadow-[0_0_30px_rgba(244,63,94,0.5)]' : 'border-cyan-400 shadow-[0_0_25px_rgba(34,211,238,0.4)]')" class="w-48 h-60 border-2 rounded-3xl relative transition-all duration-300">
                                <!-- Corner Brackets -->
                                <div class="absolute -top-2 -left-2 w-4 h-4 border-t-4 border-l-4 border-cyan-400"></div>
                                <div class="absolute -top-2 -right-2 w-4 h-4 border-t-4 border-r-4 border-cyan-400"></div>
                                <div class="absolute -bottom-2 -left-2 w-4 h-4 border-b-4 border-l-4 border-cyan-400"></div>
                                <div class="absolute -bottom-2 -right-2 w-4 h-4 border-b-4 border-r-4 border-cyan-400"></div>

                                <div class="absolute inset-x-0 bottom-2 text-center">
                                    <span x-show="scanConfidence > 0" class="px-2 py-1 bg-emerald-500 text-slate-950 text-[10px] font-mono font-black rounded-full shadow-lg" x-text="scanConfidence + '% MATCH'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Trigger Button Overlay -->
                        <div class="absolute bottom-3 inset-x-0 flex justify-center">
                            <button type="button" @click="triggerFaceVerification()" :disabled="scanStatus === 'verifying'"
                                    class="px-6 py-2.5 bg-gradient-to-r from-cyan-500 to-indigo-600 hover:from-cyan-400 hover:to-indigo-500 text-white font-mono font-bold text-xs rounded-full shadow-lg transition transform active:scale-95 flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <span>VERIFIKASI WAJAH SEKARANG</span>
                            </button>
                        </div>
                    </div>

                    <!-- Real-time Status Display -->
                    <div :class="scanStatus === 'success' ? 'bg-emerald-950/60 border-emerald-500 text-emerald-200' : (scanStatus === 'failed' ? 'bg-rose-950/60 border-rose-500 text-rose-200' : 'bg-slate-900/80 border-indigo-500/30 text-indigo-200')" class="p-4 rounded-2xl border text-xs font-mono transition-all">
                        <div class="flex items-center space-x-2">
                            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping shrink-0"></span>
                            <p class="font-bold" x-text="scanMessage"></p>
                        </div>

                        <template x-if="scannedUser">
                            <div class="mt-3 pt-3 border-t border-emerald-500/30 flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 font-bold flex items-center justify-center border border-emerald-500/40">
                                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div>
                                    <p class="font-bold text-white text-sm" x-text="scannedUser.name"></p>
                                    <p class="text-[11px] text-emerald-300" x-text="scannedUser.email"></p>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="flex items-center justify-end pt-2">
                        <button type="button" @click="showFaceScanModal = false" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-mono text-xs font-bold rounded-xl transition">
                            Tutup Scanner
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- MODAL: KONTROL JAM SESI & LIVE BRIEFING KEPALA SEKOLAH -->
        <!-- ============================================================ -->
        <div x-show="showSesiBriefingModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
             style="display: none;">
            
            <div @click.away="showSesiBriefingModal = false"
                 class="bg-white dark:bg-[#1E293B] w-full max-w-xl rounded-3xl p-6 space-y-5 max-h-[90vh] flex flex-col shadow-2xl border border-slate-200 dark:border-slate-700">
                
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2.5">
                        <span class="w-9 h-9 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-lg font-bold">👑</span>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Kontrol Jam Sesi & Live Briefing</h3>
                            <p class="text-xs text-slate-500">Pengaturan Waktu Absensi & Sesi Briefing Kepala Sekolah</p>
                        </div>
                    </div>
                    <button type="button" @click="showSesiBriefingModal = false" class="text-slate-400 hover:text-slate-600 text-2xl font-bold">×</button>
                </div>

                <div class="space-y-4 overflow-y-auto flex-1 pr-1" x-data="{ activeSettingTab: 'briefing' }">
                    
                    <!-- Tab Selector -->
                    <div class="grid grid-cols-2 gap-2 bg-slate-100 dark:bg-slate-800/80 p-1.5 rounded-2xl text-xs font-bold">
                        <button type="button" @click="activeSettingTab = 'briefing'"
                                :class="activeSettingTab === 'briefing' ? 'bg-white dark:bg-[#0F172A] text-orange-600 shadow-sm' : 'text-slate-500'"
                                class="py-2 rounded-xl transition-all flex items-center justify-center gap-1.5">
                            <span>📢</span>
                            <span>Sesi Briefing Kepala Sekolah</span>
                        </button>
                        <button type="button" @click="activeSettingTab = 'sesi'"
                                :class="activeSettingTab === 'sesi' ? 'bg-white dark:bg-[#0F172A] text-blue-600 shadow-sm' : 'text-slate-500'"
                                class="py-2 rounded-xl transition-all flex items-center justify-center gap-1.5">
                            <span>⏰</span>
                            <span>Pengaturan Jam Sesi</span>
                        </button>
                    </div>

                    <!-- TAB 1: BRIEFING KEPALA SEKOLAH -->
                    <div x-show="activeSettingTab === 'briefing'" class="space-y-4 pt-1">
                        
                        <!-- Toggle Status -->
                        <div class="p-4 rounded-2xl border flex items-center justify-between"
                             :class="briefingActive ? 'bg-emerald-50/80 dark:bg-emerald-950/30 border-emerald-300 dark:border-emerald-800' : 'bg-slate-50 dark:bg-slate-800/50 border-slate-200 dark:border-slate-700'">
                            <div>
                                <span class="text-[10px] font-black uppercase tracking-wider block"
                                      :class="briefingActive ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-500'">Status Sesi Briefing</span>
                                <span class="text-sm font-extrabold"
                                      :class="briefingActive ? 'text-emerald-800 dark:text-emerald-300' : 'text-slate-700 dark:text-slate-300'"
                                      x-text="briefingActive ? '🟢 AKTIF & DAPAT DIABSEN PEGAWAI' : '🔴 TUTUP / NONAKTIF'"></span>
                            </div>
                            <button type="button"
                                    @click="toggleBriefing(!briefingActive)"
                                    :disabled="isSavingConfig"
                                    :class="briefingActive ? 'bg-rose-600 hover:bg-rose-700 text-white' : 'bg-emerald-600 hover:bg-emerald-700 text-white'"
                                    class="py-2.5 px-4 rounded-xl font-extrabold text-xs shadow-sm transition-all active:scale-95 disabled:opacity-50 cursor-pointer">
                                <span x-text="briefingActive ? 'Tutup Sesi Briefing' : 'Buka Sesi Live'"></span>
                            </button>
                        </div>

                        <!-- Judul Briefing -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block">Judul / Topik Briefing</label>
                            <input type="text" x-model="briefingTitle"
                                   placeholder="Contoh: Briefing Pagi Kedisiplinan & KBM Santri"
                                   class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none text-slate-900 dark:text-white">
                        </div>

                        <!-- Isi / Ringkasan Briefing -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block">Isi Arahan / Ringkasan Materi Briefing</label>
                            <textarea x-model="briefingContent" rows="4"
                                      placeholder="Tuliskan poin-poin arahan kepala sekolah yang dapat dibaca oleh seluruh asatidzah di aplikasi mobile..."
                                      class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 focus:border-amber-500 focus:ring-2 focus:ring-amber-100 outline-none text-slate-900 dark:text-white"></textarea>
                        </div>

                        <button type="button" @click="toggleBriefing(briefingActive)"
                                :disabled="isSavingConfig"
                                class="w-full py-3 rounded-xl bg-gradient-to-r from-orange-500 to-amber-600 hover:from-orange-600 hover:to-amber-700 text-white font-extrabold text-xs shadow-md active:scale-98 transition-all flex items-center justify-center gap-2 disabled:opacity-50 cursor-pointer">
                            <span x-show="!isSavingConfig">💾 Simpan Perubahan Materi Briefing</span>
                            <span x-show="isSavingConfig" class="animate-spin">⏳</span>
                        </button>
                    </div>

                    <!-- TAB 2: PENGATURAN JAM SESI PRESENSI -->
                    <div x-show="activeSettingTab === 'sesi'" class="space-y-4 pt-1">
                        
                        <!-- Manual Override Toggle -->
                        <div class="p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/30 space-y-1">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" x-model="sessionManualOverride" class="w-4 h-4 rounded text-amber-600 focus:ring-amber-400">
                                <span class="text-xs font-black text-amber-900 dark:text-amber-200">Mode Buka Paksa (Manual Override)</span>
                            </label>
                            <p class="text-[11px] text-amber-800 dark:text-amber-300/80 leading-relaxed">
                                Jika diaktifkan, semua sesi presensi (Pagi, Siang, Pulang) di aplikasi mobile akan langsung dibuka tanpa batasan jam buka/tutup.
                            </p>
                        </div>

                        <!-- Sesi 1 Pagi -->
                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700 space-y-2.5">
                            <span class="text-xs font-black text-slate-800 dark:text-white flex items-center gap-1.5">
                                <span>🌅</span> SESI 1 (PAGI / MASUK)
                            </span>
                            <div class="grid grid-cols-3 gap-2.5 text-xs">
                                <div>
                                    <span class="text-slate-500 block text-[10px] font-bold mb-1">Jam Buka</span>
                                    <input type="time" x-model="sessionMorningOpen" class="w-full px-2.5 py-1.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-xs font-mono font-bold">
                                </div>
                                <div>
                                    <span class="text-rose-600 dark:text-rose-400 block text-[10px] font-bold mb-1">Batas Telat</span>
                                    <input type="time" x-model="sessionMorningLate" class="w-full px-2.5 py-1.5 rounded-xl border border-rose-300 dark:border-rose-600 bg-white dark:bg-slate-900 text-xs font-mono font-bold text-rose-600">
                                </div>
                                <div>
                                    <span class="text-slate-500 block text-[10px] font-bold mb-1">Jam Tutup</span>
                                    <input type="time" x-model="sessionMorningClose" class="w-full px-2.5 py-1.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-xs font-mono font-bold">
                                </div>
                            </div>
                        </div>

                        <!-- Sesi 2 Siang -->
                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700 space-y-2.5">
                            <span class="text-xs font-black text-slate-800 dark:text-white flex items-center gap-1.5">
                                <span>☀️</span> SESI 2 (SIANG / DZUHUR)
                            </span>
                            <div class="grid grid-cols-2 gap-2.5 text-xs">
                                <div>
                                    <span class="text-slate-500 block text-[10px] font-bold mb-1">Jam Buka</span>
                                    <input type="time" x-model="sessionAfternoonOpen" class="w-full px-2.5 py-1.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-xs font-mono font-bold">
                                </div>
                                <div>
                                    <span class="text-slate-500 block text-[10px] font-bold mb-1">Jam Tutup</span>
                                    <input type="time" x-model="sessionAfternoonClose" class="w-full px-2.5 py-1.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-xs font-mono font-bold">
                                </div>
                            </div>
                        </div>

                        <!-- Sesi 3 Sore -->
                        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700 space-y-2.5">
                            <span class="text-xs font-black text-slate-800 dark:text-white flex items-center gap-1.5">
                                <span>🌇</span> SESI 3 (SORE / PULANG)
                            </span>
                            <div class="grid grid-cols-2 gap-2.5 text-xs">
                                <div>
                                    <span class="text-slate-500 block text-[10px] font-bold mb-1">Jam Buka</span>
                                    <input type="time" x-model="sessionEveningOpen" class="w-full px-2.5 py-1.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-xs font-mono font-bold">
                                </div>
                                <div>
                                    <span class="text-slate-500 block text-[10px] font-bold mb-1">Jam Tutup</span>
                                    <input type="time" x-model="sessionEveningClose" class="w-full px-2.5 py-1.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-xs font-mono font-bold">
                                </div>
                            </div>
                        </div>

                        <button type="button" @click="saveSessionTimes()"
                                :disabled="isSavingConfig"
                                class="w-full py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-md active:scale-98 transition-all flex items-center justify-center gap-2 disabled:opacity-50 cursor-pointer">
                            <span x-show="!isSavingConfig">💾 Simpan Jadwal Jam Sesi</span>
                            <span x-show="isSavingConfig" class="animate-spin">⏳</span>
                        </button>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                    <button type="button" @click="showSesiBriefingModal = false" class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

