@extends('layouts.admin')

@section('title', 'Presensi Mandiri Guru & Pegawai')
@section('page_title', 'Presensi Mandiri')

@section('content')
<div class="space-y-6" x-data="{
    openPresensiModal: false,
    openEmployeePermitModal: false,
    openBriefingModal: false,
    briefingActive: {{ ($briefingSession && $briefingSession->is_active && $briefingSession->isOpen()) ? 'true' : 'false' }},
    briefingIsExpired: {{ ($briefingSession && $briefingSession->isExpired()) ? 'true' : 'false' }},
    briefingTitle: '{{ addslashes($briefingSession->title ?? Setting::get('briefing_title', 'Briefing Rutin Harian Pegawai & Evaluasi')) }}',
    briefingNotes: '{{ addslashes($briefingSession->notes ?? Setting::get('briefing_content', 'Bismillah. Selamat bertugas asatidzah & seluruh staf pegawai. Mohon hadir tepat waktu dan ikuti seluruh rangkaian agenda harian dengan ikhlas.')) }}',
    briefingStartTime: '{{ $briefingSession->start_time ?? Setting::get('briefing_time_start', date('H:i')) }}',
    briefingEndTime: '{{ $briefingSession->end_time ?? Setting::get('briefing_time_end', now()->setTimezone(config('app.timezone', 'Asia/Makassar'))->addMinutes(45)->format('H:i')) }}',
    hasAttendedBriefing: {{ $hasAttendedBriefing ? 'true' : 'false' }},
    attendedTime: '{{ $todayBriefingAttendance ? substr($todayBriefingAttendance->attended_at, 0, 5) : '' }}',
    briefingAttendeesCount: {{ $briefingAttendeesCount ?? 0 }},
    isSubmittingBriefing: false,
    isSavingBriefing: false,

    setDurationMinutes(mins) {
        let start = this.briefingStartTime || '{{ date('H:i') }}';
        let parts = start.split(':').map(Number);
        let total = (parts[0] || 0) * 60 + (parts[1] || 0) + mins;
        let h = Math.floor(total / 60) % 24;
        let m = total % 60;
        this.briefingEndTime = (h < 10 ? '0' + h : h) + ':' + (m < 10 ? '0' + m : m);
    },

    async attendBriefingNow() {
        if (this.isSubmittingBriefing) return;
        this.isSubmittingBriefing = true;
        try {
            const res = await fetch('{{ route('admin.teacher-attendances.attend-briefing') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    device_info: navigator.userAgent
                })
            });
            const data = await res.json();
            if (data.success) {
                this.hasAttendedBriefing = true;
                this.attendedTime = data.attended_at || '{{ date('H:i') }}';
                this.briefingAttendeesCount++;
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Kehadiran Briefing Berhasil!',
                        text: data.message,
                        confirmButtonColor: '#4F46E5',
                        timer: 2500
                    });
                } else {
                    alert(data.message);
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Absen Briefing',
                        text: data.message || 'Terjadi kesalahan saat memproses presensi briefing.',
                        confirmButtonColor: '#4F46E5'
                    });
                } else {
                    alert(data.message || 'Gagal memproses absensi.');
                }
            }
        } catch (e) {
            alert('Gagal menghubungi server. Silakan coba kembali.');
        } finally {
            this.isSubmittingBriefing = false;
        }
    },

    async saveBriefingSession(activate) {
        if (this.isSavingBriefing) return;
        this.isSavingBriefing = true;
        try {
            const res = await fetch('{{ route('admin.teacher-attendances.toggle-briefing') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    is_active: activate,
                    title: this.briefingTitle,
                    notes: this.briefingNotes,
                    start_time: this.briefingStartTime,
                    end_time: this.briefingEndTime
                })
            });
            const data = await res.json();
            if (data.success) {
                this.briefingActive = data.briefing.active && data.briefing.is_open;
                this.briefingIsExpired = data.briefing.is_expired;
                this.briefingTitle = data.briefing.title;
                this.briefingNotes = data.briefing.notes;
                this.briefingStartTime = data.briefing.start_time;
                this.briefingEndTime = data.briefing.end_time;
                this.openBriefingModal = false;

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: activate ? 'Sesi Briefing Dibuka!' : 'Sesi Briefing Ditutup',
                        text: data.message,
                        confirmButtonColor: '#4F46E5',
                        timer: 2200
                    });
                } else {
                    alert(data.message);
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Mengubah Sesi',
                        text: data.message || 'Terjadi kesalahan saat menyimpan sesi briefing.',
                        confirmButtonColor: '#4F46E5'
                    });
                } else {
                    alert(data.message || 'Gagal menyimpan sesi.');
                }
            }
        } catch (e) {
            alert('Gagal menghubungi server.');
        } finally {
            this.isSavingBriefing = false;
        }
    }
}">

    <!-- ═════════════════════════════════════════════════════════════════════ -->
    <!-- HEADER & PROFILE CARD -->
    <!-- ═════════════════════════════════════════════════════════════════════ -->
    <div class="p-6 rounded-3xl bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-14 h-14 rounded-2xl text-white flex items-center justify-center font-black text-xl shadow-md shrink-0"
                 style="background: linear-gradient(135deg, {{ Setting::get('primary_color', '#3C50E0') }} 0%, {{ Setting::get('secondary_color', '#2563eb') }} 100%);">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 dark:text-white">{{ $user->name }}</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 uppercase">
                        {{ $user->roles->pluck('name')->implode(', ') ?: 'Pendidik' }}
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 dark:text-slate-400 mt-1">
                    @if($user->nip)
                        <span class="font-mono">NIP: {{ $user->nip }}</span>
                        <span>&bull;</span>
                    @endif
                    <span>{{ $user->email }}</span>
                    <span>&bull;</span>
                    <span>{{ $schoolName }}</span>
                </div>
            </div>
        </div>

        <!-- Right: Action Buttons & Live Server Clock -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 shrink-0">
            <!-- Button: Absen Mandiri GPS Popup -->
            <button type="button" @click.stop="openPresensiModal = true"
                    class="px-4 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white font-extrabold text-xs flex items-center justify-center gap-2 transition-all shadow-md shadow-indigo-600/20 cursor-pointer">
                <span class="w-6 h-6 rounded-lg bg-white/20 flex items-center justify-center text-sm">📍</span>
                <span>Absen Mandiri GPS</span>
            </button>

            <!-- Button: Ajukan Izin / Sakit Popup -->
            <button type="button" @click.stop="openEmployeePermitModal = true"
                    class="px-4 py-3 rounded-2xl bg-indigo-50 dark:bg-indigo-950/50 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 text-xs font-extrabold flex items-center justify-center gap-2 transition-all shadow-xs cursor-pointer">
                <span>📝</span>
                <span>Ajukan Izin / Sakit</span>
            </button>

            <!-- Live Server Clock (WITA) -->
            <div class="p-3 rounded-2xl bg-slate-50 dark:bg-[#24303F] border border-slate-200 dark:border-slate-700/60 text-right min-w-[150px]">
                <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Waktu Server ({{ $timezoneLabel }})</span>
                <span class="text-xl font-black font-mono text-slate-900 dark:text-white" id="live-server-clock">
                    {{ now()->setTimezone(config('app.timezone', 'Asia/Makassar'))->format('H:i:s') }}
                </span>
            </div>
        </div>
    </div>

    @if($todayAttendance && in_array($todayAttendance->status, ['sick', 'permission']))
    <!-- Status Izin Resmi Hari Ini -->
    <div class="p-4 rounded-2xl bg-indigo-50/80 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800/60 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-base shadow-sm">
                📋
            </span>
            <div>
                <h4 class="text-xs font-extrabold text-indigo-900 dark:text-indigo-200">Status Kehadiran Hari Ini: {{ $todayAttendance->status_label }}</h4>
                <p class="text-[11px] text-indigo-700 dark:text-indigo-300">{{ $todayAttendance->notes }}</p>
            </div>
        </div>
        <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-300/40 shrink-0">
            Resmi Terverifikasi
        </span>
    </div>
    @endif

    <!-- ═════════════════════════════════════════════════════════════════════ -->
    <!-- KARTU SESI BRIEFING (PERSIS GAMBAR 1 - KENDALI KEPALA SEKOLAH & ADMIN) -->
    <!-- ═════════════════════════════════════════════════════════════════════ -->
    <div class="tailadmin-card p-6 sm:p-7 bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xs space-y-4">
        <!-- Header Row -->
        <div class="flex items-center justify-between">
            <span class="text-xs sm:text-sm font-black text-[#4F46E5] dark:text-indigo-400 tracking-wider uppercase">
                SESI BRIEFING
            </span>

            <!-- Status Badge -->
            <div>
                <template x-if="briefingActive && !briefingIsExpired">
                    <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-300/40 inline-flex items-center gap-1.5 animate-pulse">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Sedang Dibuka
                    </span>
                </template>
                <template x-if="!briefingActive && !briefingIsExpired">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                        Belum Dibuka
                    </span>
                </template>
                <template x-if="briefingIsExpired || (!briefingActive && {{ ($briefingSession && $briefingSession->is_active === false) ? 'true' : 'false' }})">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300">
                        Sesi Ditutup
                    </span>
                </template>
            </div>
        </div>

        <div>
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight uppercase">
                ABSEN BRIEFING
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-semibold mt-0.5">
                Sesi Dikendalikan Kepala Sekolah
            </p>
        </div>

        <div class="border-t border-slate-100 dark:border-slate-800 pt-2">

            <!-- KONDISI 1: SESI BELUM DIBUKA (PERSIS GAMBAR 1) -->
            <div x-show="!briefingActive && !briefingIsExpired" class="border-2 border-dashed border-indigo-200/90 dark:border-indigo-900/60 rounded-3xl p-6 sm:p-8 text-center bg-indigo-50/20 dark:bg-indigo-950/10">
                <div class="max-w-md mx-auto space-y-3">
                    <h3 class="text-sm sm:text-base font-extrabold text-slate-700 dark:text-slate-200 flex items-center justify-center gap-2">
                        <span>⏳</span>
                        <span>SESI BRIEFING BELUM DIBUKA</span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Sesi absensi briefing belum dibuka untuk para guru hari ini.
                    </p>

                    @if($canManageBriefing)
                        <!-- Tombol untuk Kepala Sekolah & Admin (Gambar 1) -->
                        <div class="pt-2">
                            <button type="button" @click="openBriefingModal = true"
                                    class="inline-flex items-center justify-center gap-2.5 px-8 py-3.5 rounded-2xl bg-[#4F46E5] hover:bg-[#4338CA] active:scale-95 text-white font-extrabold text-xs sm:text-sm shadow-lg shadow-indigo-600/30 transition-all cursor-pointer">
                                <span class="text-base">🔔</span>
                                <span>BUKA / AKTIFKAN SESI</span>
                            </button>
                        </div>
                    @else
                        <p class="text-[11px] text-slate-400 dark:text-slate-500 italic mt-2">
                            Menunggu Kepala Sekolah atau Admin mengaktifkan sesi absensi briefing.
                        </p>
                    @endif
                </div>
            </div>

            <!-- KONDISI 2: SESI SEDANG DIBUKA / AKTIF -->
            <div x-show="briefingActive && !briefingIsExpired" class="border border-indigo-200 dark:border-indigo-800 rounded-3xl p-5 sm:p-6 bg-gradient-to-br from-indigo-50/50 via-white to-purple-50/30 dark:from-indigo-950/30 dark:via-[#1A222C] dark:to-purple-950/20 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-indigo-100 dark:border-indigo-900/40">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 block">Topik Briefing Hari Ini</span>
                        <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white" x-text="briefingTitle"></h3>
                    </div>
                    <div class="flex items-center gap-2 text-xs font-bold text-slate-600 dark:text-slate-300 shrink-0">
                        <span class="px-3 py-1.5 rounded-xl bg-white dark:bg-[#24303F] border border-slate-200 dark:border-slate-700 shadow-2xs font-mono">
                            ⏰ <span x-text="briefingStartTime"></span> - <span x-text="briefingEndTime"></span> {{ $timezoneLabel }}
                        </span>
                    </div>
                </div>

                <!-- Catatan / Poin Instruksi -->
                <div class="p-4 rounded-2xl bg-white/80 dark:bg-[#24303F] border border-slate-200/80 dark:border-slate-700 text-xs sm:text-sm text-slate-700 dark:text-slate-300 leading-relaxed italic">
                    <span class="font-bold not-italic text-slate-900 dark:text-white block mb-1">📢 Arahan / Poin Materi:</span>
                    <p x-text="briefingNotes"></p>
                </div>

                <!-- Tombol Absen Briefing atau Status Sudah Hadir -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-2">
                    <!-- Sisi Kiri: Status Kehadiran Pegawai -->
                    <div>
                        <template x-if="hasAttendedBriefing">
                            <div class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-200 border border-emerald-300/40 text-xs font-black">
                                <span>✅</span>
                                <span>SUDAH HADIR BRIEFING <span x-show="attendedTime" x-text="'(Pukul ' + attendedTime + ' {{ $timezoneLabel }})'"></span></span>
                            </div>
                        </template>

                        <template x-if="!hasAttendedBriefing">
                            <button type="button" @click="attendBriefingNow()" :disabled="isSubmittingBriefing"
                                    class="inline-flex items-center justify-center gap-2.5 px-7 py-3.5 rounded-2xl bg-[#4F46E5] hover:bg-[#4338CA] active:scale-95 text-white font-extrabold text-xs sm:text-sm shadow-lg shadow-indigo-600/30 transition cursor-pointer">
                                <span class="text-base" x-show="!isSubmittingBriefing">📝</span>
                                <span class="animate-spin text-sm" x-show="isSubmittingBriefing">⏳</span>
                                <span x-text="isSubmittingBriefing ? 'Menyimpan...' : 'ISI ABSEN BRIEFING SEKARANG'"></span>
                            </button>
                        </template>
                    </div>

                    <!-- Sisi Kanan: Panel Kontrol Kepala Sekolah / Admin -->
                    @if($canManageBriefing)
                        <div class="flex items-center gap-2.5">
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400" x-text="briefingAttendeesCount + ' Pegawai Telah Hadir'"></span>
                            <button type="button" @click="openBriefingModal = true"
                                    class="px-4 py-2.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 text-xs font-extrabold transition cursor-pointer flex items-center gap-1.5">
                                <span>⚙️</span>
                                <span>Kelola Sesi</span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- KONDISI 3: SESI TELAH DITUTUP / WAKTU HABIS -->
            <div x-show="briefingIsExpired || (!briefingActive && {{ ($briefingSession && $briefingSession->is_active === false) ? 'true' : 'false' }})" class="border border-slate-200 dark:border-slate-800 rounded-3xl p-6 text-center bg-slate-50 dark:bg-slate-800/30 space-y-3">
                <div class="max-w-md mx-auto space-y-2">
                    <h3 class="text-sm sm:text-base font-extrabold text-slate-700 dark:text-slate-300 flex items-center justify-center gap-2">
                        <span>🔒</span>
                        <span>SESI BRIEFING TELAH DITUTUP</span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Batas waktu absensi briefing telah selesai untuk hari ini (Pukul <span x-text="briefingEndTime"></span> {{ $timezoneLabel }}).
                    </p>

                    <template x-if="hasAttendedBriefing">
                        <div class="mt-2 inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 text-xs font-bold">
                            ✓ Anda telah tercatat hadir pada pukul <span x-text="attendedTime"></span> {{ $timezoneLabel }}
                        </div>
                    </template>

                    @if($canManageBriefing)
                        <!-- Kepala Sekolah / Admin dapat membuka kembali sesi kapan saja -->
                        <div class="pt-3">
                            <button type="button" @click="openBriefingModal = true"
                                    class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-[#4F46E5] hover:bg-[#4338CA] active:scale-95 text-white font-extrabold text-xs shadow-md shadow-indigo-600/20 transition cursor-pointer">
                                <span>🔔</span>
                                <span>BUKA KEMBALI SESI BRIEFING</span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <!-- ═════════════════════════════════════════════════════════════════════ -->
    <!-- 3 KARTU SESI PRESENSI HARI INI -->
    <!-- ═════════════════════════════════════════════════════════════════════ -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- 1. Pagi (Masuk) -->
        <div class="tailadmin-card p-5 border rounded-2xl shadow-xs transition-all {{ ($todayAttendance && $todayAttendance->check_in) ? 'bg-emerald-50/40 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800/40' : 'bg-white dark:bg-[#1A222C] border-slate-200 dark:border-slate-800' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">1. Sesi Pagi (Masuk)</span>
                @if($todayAttendance && $todayAttendance->check_in)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300">✓ Sudah Hadir</span>
                @else
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-500">Belum Absen</span>
                @endif
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <div>
                    <p class="text-2xl font-black font-mono {{ ($todayAttendance && $todayAttendance->check_in) ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }}">
                        {{ $todayAttendance && $todayAttendance->check_in ? substr($todayAttendance->check_in, 0, 5) : '--:--' }}
                    </p>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                        Batas: {{ $attendanceSettings['morning_late'] ?? '07:30' }} {{ $timezoneLabel }} (Buka: {{ $attendanceSettings['morning_open'] ?? '06:00' }} - {{ $attendanceSettings['morning_close'] ?? '11:59' }})
                    </span>
                </div>
                <div class="w-10 h-10 rounded-xl {{ ($todayAttendance && $todayAttendance->check_in) ? 'bg-emerald-500/10 text-emerald-600' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }} flex items-center justify-center font-bold">
                    🌅
                </div>
            </div>
        </div>

        <!-- 2. Siang (Dzuhur) -->
        <div class="tailadmin-card p-5 border rounded-2xl shadow-xs transition-all {{ ($todayAttendance && $todayAttendance->midday_at) ? 'bg-amber-50/40 dark:bg-amber-950/20 border-amber-200 dark:border-amber-800/40' : 'bg-white dark:bg-[#1A222C] border-slate-200 dark:border-slate-800' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">2. Sesi Siang (Dzuhur)</span>
                @if($todayAttendance && $todayAttendance->midday_at)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100 dark:bg-amber-900/60 text-amber-700 dark:text-amber-300">✓ Sudah Hadir</span>
                @else
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-500">Belum Absen</span>
                @endif
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <div>
                    <p class="text-2xl font-black font-mono {{ ($todayAttendance && $todayAttendance->midday_at) ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400' }}">
                        {{ $todayAttendance && $todayAttendance->midday_at ? substr($todayAttendance->midday_at, 0, 5) : '--:--' }}
                    </p>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                        Jadwal: {{ $attendanceSettings['dzuhur_open'] ?? '12:00' }} - {{ $attendanceSettings['dzuhur_close'] ?? '13:30' }} {{ $timezoneLabel }}
                    </span>
                </div>
                <div class="w-10 h-10 rounded-xl {{ ($todayAttendance && $todayAttendance->midday_at) ? 'bg-amber-500/10 text-amber-600' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }} flex items-center justify-center font-bold">
                    ☀️
                </div>
            </div>
        </div>

        <!-- 3. Sore (Pulang) -->
        <div class="tailadmin-card p-5 border rounded-2xl shadow-xs transition-all {{ ($todayAttendance && $todayAttendance->check_out) ? 'bg-indigo-50/40 dark:bg-indigo-950/20 border-indigo-200 dark:border-indigo-800/40' : 'bg-white dark:bg-[#1A222C] border-slate-200 dark:border-slate-800' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400">3. Sesi Sore (Pulang)</span>
                @if($todayAttendance && $todayAttendance->check_out)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300">✓ Sudah Pulang</span>
                @else
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-500">Belum Pulang</span>
                @endif
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <div>
                    <p class="text-2xl font-black font-mono {{ ($todayAttendance && $todayAttendance->check_out) ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400' }}">
                        {{ $todayAttendance && $todayAttendance->check_out ? substr($todayAttendance->check_out, 0, 5) : '--:--' }}
                    </p>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                        KBM Selesai: {{ $attendanceSettings['afternoon_open'] ?? '14:00' }} - {{ $attendanceSettings['afternoon_close'] ?? '18:00' }} {{ $timezoneLabel }}
                    </span>
                </div>
                <div class="w-10 h-10 rounded-xl {{ ($todayAttendance && $todayAttendance->check_out) ? 'bg-indigo-500/10 text-indigo-600' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }} flex items-center justify-center font-bold">
                    🌇
                </div>
            </div>
        </div>
    </div>

    <!-- ═════════════════════════════════════════════════════════════════════ -->
    <!-- REKAP KEHADIRAN BULAN INI -->
    <!-- ═════════════════════════════════════════════════════════════════════ -->
    <div class="tailadmin-card p-6 bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 rounded-3xl shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <div>
                <h4 class="text-xs font-extrabold text-slate-900 dark:text-white uppercase tracking-wider">Rekap Kehadiran Bulan Ini</h4>
                <p class="text-[11px] text-slate-500">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Persentase:</span>
                <span class="text-base font-black text-indigo-600 dark:text-indigo-400">{{ $attendanceRate }}%</span>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
            <div class="p-4 rounded-2xl bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200/60 dark:border-emerald-800/40">
                <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-300 uppercase block">Tepat Waktu</span>
                <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400 font-mono mt-1 block">{{ $presentCount }} Hari</span>
            </div>
            <div class="p-4 rounded-2xl bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200/60 dark:border-amber-800/40">
                <span class="text-[10px] font-bold text-amber-700 dark:text-amber-300 uppercase block">Terlambat</span>
                <span class="text-2xl font-black text-amber-600 dark:text-amber-400 font-mono mt-1 block">{{ $lateCount }} Hari</span>
            </div>
            <div class="p-4 rounded-2xl bg-blue-50/70 dark:bg-blue-950/30 border border-blue-200/60 dark:border-blue-800/40">
                <span class="text-[10px] font-bold text-blue-700 dark:text-blue-300 uppercase block">Sakit</span>
                <span class="text-2xl font-black text-blue-600 dark:text-blue-400 font-mono mt-1 block">{{ $sickCount }} Hari</span>
            </div>
            <div class="p-4 rounded-2xl bg-purple-50/70 dark:bg-purple-950/30 border border-purple-200/60 dark:border-purple-800/40">
                <span class="text-[10px] font-bold text-purple-700 dark:text-purple-300 uppercase block">Izin</span>
                <span class="text-2xl font-black text-purple-600 dark:text-purple-400 font-mono mt-1 block">{{ $permissionCount }} Hari</span>
            </div>
        </div>
    </div>

    <!-- ═════════════════════════════════════════════════════════════════════ -->
    <!-- RIWAYAT KEHADIRAN (14 HARI TERAKHIR) -->
    <!-- ═════════════════════════════════════════════════════════════════════ -->
    <div class="tailadmin-card bg-white dark:bg-[#1A222C] border border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden shadow-xs">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <h3 class="font-extrabold text-slate-900 dark:text-white text-sm uppercase tracking-wider">Riwayat Kehadiran Terakhir (14 Hari Terakhir)</h3>
            <span class="text-xs text-slate-500 font-medium">Data Kehadiran Pribadi Anda</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-[#24303F] text-slate-500 dark:text-slate-400 uppercase tracking-wider font-bold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-3.5">Tanggal</th>
                        <th class="px-6 py-3.5">Pagi (Masuk)</th>
                        <th class="px-6 py-3.5">Siang (Dzuhur)</th>
                        <th class="px-6 py-3.5">Sore (Pulang)</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">Moda / Lokasi</th>
                        <th class="px-6 py-3.5">Metode</th>
                        <th class="px-6 py-3.5 text-right">Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-800 dark:text-slate-200">
                    @forelse($recentAttendances as $att)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4 font-bold font-mono">
                                {{ \Carbon\Carbon::parse($att->date)->translatedFormat('d F Y') }}
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                {{ $att->check_in ? substr($att->check_in, 0, 5) : '-' }}
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-amber-600 dark:text-amber-400">
                                {{ $att->midday_at ? substr($att->midday_at, 0, 5) : '-' }}
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-indigo-600 dark:text-indigo-400">
                                {{ $att->check_out ? substr($att->check_out, 0, 5) : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($att->status === 'present')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">Tepat Waktu</span>
                                @elseif($att->status === 'late')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800">Terlambat</span>
                                @elseif($att->status === 'sick')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400">Sakit</span>
                                @elseif($att->status === 'permission')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400">Izin</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-600">Alpa</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="capitalize font-medium">{{ $att->work_location === 'outstation' ? 'Dinas Luar' : ($att->work_location === 'home' ? 'WFH' : 'WFO Sekolah') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold {{ $att->method === 'fingerprint' ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300' : 'bg-cyan-100 dark:bg-cyan-950 text-cyan-700 dark:text-cyan-300' }}">
                                    {{ $att->method === 'fingerprint' ? 'Sidik Jari USB' : 'GPS Mobile' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($att->verification_status)
                                    <span class="text-emerald-600 dark:text-emerald-400 font-extrabold text-[11px] inline-flex items-center gap-1">
                                        ✓ Terverifikasi
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-slate-400 text-xs">
                                Belum ada riwayat presensi yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ═════════════════════════════════════════════════════════════════════ -->
    <!-- UNIVERSAL POPUP MODALS -->
    <!-- ═════════════════════════════════════════════════════════════════════ -->
    @include('components.dashboard.presensi-modal')
    @include('components.dashboard.employee-permit-modal')

    <!-- ═════════════════════════════════════════════════════════════════════ -->
    <!-- MODAL: KELOLA SESI BRIEFING (PERSIS MOCKUP GAMBAR 2) -->
    <!-- ═════════════════════════════════════════════════════════════════════ -->
    @if($canManageBriefing)
    <div x-show="openBriefingModal"
         x-cloak
         class="fixed inset-0 z-9999 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
         @keydown.escape.window="openBriefingModal = false">

        <div @click.away="openBriefingModal = false"
             class="bg-white dark:bg-[#1A222C] rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200 dark:border-slate-800 transition-all transform animate-in fade-in zoom-in duration-200">

            <!-- HEADER BANNER UNGU/INDIGO (PERSIS GAMBAR 2) -->
            <div class="bg-gradient-to-r from-[#4F46E5] to-[#4338CA] p-5 sm:p-6 text-white flex items-center justify-between">
                <div class="flex items-center space-x-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-white/20 flex items-center justify-center text-xl shrink-0 shadow-xs">
                        🔔
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-black tracking-tight text-white">Kelola Sesi Briefing</h3>
                        <p class="text-xs text-indigo-100 font-medium">Kendalikan ketersediaan tombol absensi & catatan harian</p>
                    </div>
                </div>
                <button type="button" @click="openBriefingModal = false"
                        class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 text-white flex items-center justify-center font-bold text-base transition cursor-pointer">
                    ✕
                </button>
            </div>

            <!-- MODAL BODY -->
            <div class="p-6 space-y-5 max-h-[80vh] overflow-y-auto">

                <!-- STATUS SESI HARI INI CARD -->
                <div class="p-4 rounded-2xl border transition-all"
                     :class="briefingActive ? 'bg-indigo-50/70 dark:bg-indigo-950/30 border-indigo-200 dark:border-indigo-800' : 'bg-slate-50 dark:bg-slate-800/40 border-slate-200 dark:border-slate-700'">
                    <span class="text-[10px] font-black uppercase tracking-wider block"
                          :class="briefingActive ? 'text-indigo-900 dark:text-indigo-300' : 'text-slate-600 dark:text-slate-400'">
                        STATUS SESI HARI INI:
                    </span>
                    <div class="flex items-center justify-between mt-2">
                        <div class="flex items-center gap-2 font-bold text-xs sm:text-sm text-slate-800 dark:text-white">
                            <span class="w-3 h-3 rounded-full" :class="briefingActive ? 'bg-emerald-500 animate-pulse' : 'bg-purple-400'"></span>
                            <span x-text="briefingActive ? '🟢 Sesi Sedang Aktif / Dibuka' : '🟣 Sesi Ditutup / Belum Dibuka'"></span>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-black tracking-wider uppercase"
                              :class="briefingActive ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300' : 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300'"
                              x-text="briefingActive ? 'AKTIF' : 'NONAKTIF'">
                        </span>
                    </div>
                </div>

                <!-- JUDUL / TOPIK BRIEFING (PERSIS GAMBAR 2) -->
                <div>
                    <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-600 dark:text-slate-400 block mb-1.5">
                        JUDUL / TOPIK BRIEFING
                    </label>
                    <input type="text" x-model="briefingTitle"
                           class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#1A222C] text-slate-900 dark:text-white font-bold text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                           placeholder="Briefing Rutin Harian Pegawai & Evaluasi">
                </div>

                <!-- POIN INSTRUKSI / MATERI BRIEFING (PERSIS GAMBAR 2) -->
                <div>
                    <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-600 dark:text-slate-400 block mb-1.5">
                        POIN INSTRUKSI / MATERI BRIEFING
                    </label>
                    <textarea x-model="briefingNotes" rows="4"
                              class="w-full p-4 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#1A222C] text-slate-800 dark:text-slate-200 text-xs sm:text-sm leading-relaxed focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition resize-none"
                              placeholder="Bismillah. Selamat bertugas asatidzah & seluruh staf pegawai. Mohon hadir tepat waktu dan ikuti seluruh rangkaian agenda harian dengan ikhlas."></textarea>
                </div>

                <!-- BATAS WAKTU BUKA DAN TUTUP -->
                <div>
                    <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-600 dark:text-slate-400 block mb-1.5">
                        BATAS WAKTU BUKA DAN TUTUP ({{ $timezoneLabel }})
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <span class="text-[10px] text-slate-500 font-semibold block mb-1">Jam Buka Sesi</span>
                            <input type="time" x-model="briefingStartTime"
                                   class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#1A222C] text-slate-800 dark:text-white font-mono font-bold text-xs">
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-500 font-semibold block mb-1">Batas Jam Tutup</span>
                            <input type="time" x-model="briefingEndTime"
                                   class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#1A222C] text-slate-800 dark:text-white font-mono font-bold text-xs">
                        </div>
                    </div>

                    <!-- Tombol Durasi Cepat -->
                    <div class="flex items-center gap-1.5 mt-2">
                        <span class="text-[10px] text-slate-400 font-medium mr-1">Atur Durasi:</span>
                        <button type="button" @click="setDurationMinutes(15)" class="px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-950 text-[10px] font-bold text-slate-600 dark:text-slate-300 hover:text-indigo-600 transition">+15 Menit</button>
                        <button type="button" @click="setDurationMinutes(30)" class="px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-950 text-[10px] font-bold text-slate-600 dark:text-slate-300 hover:text-indigo-600 transition">+30 Menit</button>
                        <button type="button" @click="setDurationMinutes(45)" class="px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-950 text-[10px] font-bold text-slate-600 dark:text-slate-300 hover:text-indigo-600 transition">+45 Menit</button>
                        <button type="button" @click="setDurationMinutes(60)" class="px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-950 text-[10px] font-bold text-slate-600 dark:text-slate-300 hover:text-indigo-600 transition">+1 Jam</button>
                    </div>
                </div>

                <!-- TOMBOL AKSI UTAMA (PERSIS GAMBAR 2) -->
                <div class="pt-2 space-y-2">
                    <template x-if="!briefingActive">
                        <button type="button" @click="saveBriefingSession(true)" :disabled="isSavingBriefing"
                                class="w-full py-4 rounded-2xl bg-[#4F46E5] hover:bg-[#4338CA] active:scale-[0.98] text-white font-black text-sm shadow-xl shadow-indigo-600/30 transition cursor-pointer flex items-center justify-center gap-2">
                            <span x-show="!isSavingBriefing">✓ BUKA & AKTIFKAN SESI</span>
                            <span x-show="isSavingBriefing" class="animate-spin text-base">⏳</span>
                            <span x-show="isSavingBriefing">Mengaktifkan Sesi...</span>
                        </button>
                    </template>

                    <template x-if="briefingActive">
                        <div class="space-y-2">
                            <button type="button" @click="saveBriefingSession(true)" :disabled="isSavingBriefing"
                                    class="w-full py-3.5 rounded-2xl bg-[#4F46E5] hover:bg-[#4338CA] active:scale-[0.98] text-white font-black text-xs sm:text-sm shadow-lg shadow-indigo-600/25 transition cursor-pointer flex items-center justify-center gap-2">
                                <span x-show="!isSavingBriefing">💾 SIMPAN PERUBAHAN SESI</span>
                                <span x-show="isSavingBriefing" class="animate-spin text-base">⏳</span>
                                <span x-show="isSavingBriefing">Menyimpan...</span>
                            </button>
                            <button type="button" @click="saveBriefingSession(false)" :disabled="isSavingBriefing"
                                    class="w-full py-3 rounded-2xl bg-rose-50 dark:bg-rose-950/40 hover:bg-rose-100 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900/60 font-bold text-xs transition cursor-pointer flex items-center justify-center gap-1.5">
                                <span>✕ TUTUP SESI BRIEFING</span>
                            </button>
                        </div>
                    </template>
                </div>

            </div>

        </div>
    </div>
    @endif

</div>
@endsection

@section('scripts')
<script>
// Standalone, self-running realtime live ticking clock (WITA)
(function() {
    let serverTimeStr = "{{ now()->setTimezone(config('app.timezone', 'Asia/Makassar'))->format('H:i:s') }}";
    let parts = serverTimeStr.split(':').map(Number);
    let totalSeconds = (parts[0] * 3600) + (parts[1] * 60) + (parts[2] || 0);

    function pad(n) {
        return n < 10 ? '0' + n : n;
    }

    function tick() {
        totalSeconds = (totalSeconds + 1) % 86400;
        let h = Math.floor(totalSeconds / 3600);
        let m = Math.floor((totalSeconds % 3600) / 60);
        let s = totalSeconds % 60;
        let timeFormatted = pad(h) + ':' + pad(m) + ':' + pad(s);

        let clockEl = document.getElementById('live-server-clock');
        if (clockEl) {
            clockEl.textContent = timeFormatted;
        }
    }

    setInterval(tick, 1000);
})();
</script>
@endsection
