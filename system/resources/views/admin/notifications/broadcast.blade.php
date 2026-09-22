@extends('layouts.admin')

@section('title', 'Kirim Notifikasi & Pengingat Presensi')
@section('page_title', 'Siaran Notifikasi (Broadcast)')

@section('content')
<div class="space-y-6" x-data="{
    targetType: 'all',
    title: '',
    message: '',
    type: 'info',
    actionUrl: '',
    selectedRoles: ['guru', 'staff'],
    selectedClass: '',
    selectedUsers: [],
    getTypeBadgeClass() {
        if (this.type === 'warning') return 'bg-amber-500 text-white';
        if (this.type === 'urgent') return 'bg-rose-500 text-white';
        if (this.type === 'attendance_reminder') return 'bg-blue-600 text-white';
        if (this.type === 'success') return 'bg-emerald-600 text-white';
        return 'bg-indigo-600 text-white';
    },
    getTypeLabel() {
        if (this.type === 'warning') return 'Peringatan';
        if (this.type === 'urgent') return 'Penting';
        if (this.type === 'attendance_reminder') return 'Pengingat Presensi';
        if (this.type === 'success') return 'Sukses';
        return 'Informasi';
    }
}">

    <!-- Header & Action Ribbon -->
    <div class="bg-white dark:bg-[#24303F] rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-xs">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="p-2 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                        </svg>
                    </span>
                    <h1 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        Siaran Notifikasi & Pengingat Presensi
                    </h1>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Kirimkan pemberitahuan push langsung ke ponsel dan PC pengguna, serta jalankan pengingat presensi otomatis.
                </p>
            </div>

            <!-- Tombol Pengingat Presensi Cepat -->
            <div class="flex flex-wrap items-center gap-2.5">
                <form method="POST" action="{{ route('admin.notifications.attendance-reminder') }}" onsubmit="return confirm('Kirim pengingat presensi pagi ke semua guru/pegawai yang belum check-in sekarang?')">
                    @csrf
                    <input type="hidden" name="session" value="morning">
                    <button type="submit" class="inline-flex items-center space-x-1.5 px-3.5 py-2 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Kirim Reminder Pagi</span>
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.notifications.attendance-reminder') }}" onsubmit="return confirm('Kirim pengingat presensi sore/pulang ke semua guru/pegawai yang belum check-out sekarang?')">
                    @csrf
                    <input type="hidden" name="session" value="afternoon">
                    <button type="submit" class="inline-flex items-center space-x-1.5 px-3.5 py-2 bg-amber-600 hover:bg-amber-700 active:scale-95 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Kirim Reminder Sore</span>
                    </button>
                </form>

                <a href="{{ route('admin.notifications.index') }}" class="inline-flex items-center space-x-1.5 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs rounded-xl transition border border-slate-200 dark:border-slate-700">
                    <span>Lihat Log Notifikasi</span>
                </a>
            </div>
        </div>

        <!-- Metric Counters -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6 pt-5 border-t border-slate-100 dark:border-slate-800">
            <div class="flex items-center space-x-3.5 p-3.5 bg-slate-50 dark:bg-[#1A222C] rounded-xl border border-slate-100 dark:border-slate-800">
                <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <div>
                    <span class="text-lg font-black text-slate-900 dark:text-white block leading-tight">{{ number_format($totalBroadcasts) }}</span>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Total Siaran Terkirim</span>
                </div>
            </div>

            <div class="flex items-center space-x-3.5 p-3.5 bg-slate-50 dark:bg-[#1A222C] rounded-xl border border-slate-100 dark:border-slate-800">
                <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-lg font-black text-slate-900 dark:text-white block leading-tight">{{ number_format($totalPushedRecipients) }}</span>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Total Penerima Terjangkau</span>
                </div>
            </div>

            <div class="flex items-center space-x-3.5 p-3.5 bg-slate-50 dark:bg-[#1A222C] rounded-xl border border-slate-100 dark:border-slate-800">
                <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-lg font-black text-slate-900 dark:text-white block leading-tight">{{ number_format($activePushDevices) }}</span>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Perangkat HP & PC Terdaftar</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content: Compose & Preview -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left: Form Pembuatan Siaran -->
        <div class="lg:col-span-7 bg-white dark:bg-[#24303F] rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-xs">
            <h2 class="text-base font-extrabold text-slate-900 dark:text-white mb-5 flex items-center gap-2">
                <span>📝</span> Buat Pesan Notifikasi Baru
            </h2>

            <form action="{{ route('admin.notifications.broadcast.send') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Judul Notifikasi -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Judul Pemberitahuan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           name="title"
                           x-model="title"
                           required
                           placeholder="Contoh: Pengingat Rapat Evaluasi Mingguan"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#1A222C] text-slate-800 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition">
                </div>

                <!-- Isi Pesan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Isi Pesan Notifikasi <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="message"
                              x-model="message"
                              rows="3"
                              required
                              placeholder="Tuliskan pesan notifikasi yang akan muncul di layar ponsel dan PC pengguna..."
                              class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#1A222C] text-slate-800 dark:text-white text-xs font-normal focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition resize-none"></textarea>
                    <div class="text-[11px] text-slate-400 mt-1 flex justify-between">
                        <span>Pesan ringkas lebih mudah dibaca pada notifikasi layar kunci HP.</span>
                        <span x-text="message.length + ' karakter'"></span>
                    </div>
                </div>

                <!-- Tipe Notifikasi -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Tipe / Prioritas Notifikasi
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                        <label class="flex items-center space-x-2.5 p-3 rounded-xl border cursor-pointer transition"
                               :class="type === 'info' ? 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-900/20 font-bold text-indigo-700 dark:text-indigo-400' : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400'">
                            <input type="radio" name="type" value="info" x-model="type" class="text-primary focus:ring-0">
                            <span class="text-xs">ℹ️ Informasi</span>
                        </label>
                        <label class="flex items-center space-x-2.5 p-3 rounded-xl border cursor-pointer transition"
                               :class="type === 'attendance_reminder' ? 'border-blue-500 bg-blue-50/50 dark:bg-blue-900/20 font-bold text-blue-700 dark:text-blue-400' : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400'">
                            <input type="radio" name="type" value="attendance_reminder" x-model="type" class="text-blue-600 focus:ring-0">
                            <span class="text-xs">⏰ Pengingat</span>
                        </label>
                        <label class="flex items-center space-x-2.5 p-3 rounded-xl border cursor-pointer transition"
                               :class="type === 'warning' ? 'border-amber-500 bg-amber-50/50 dark:bg-amber-900/20 font-bold text-amber-700 dark:text-amber-400' : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400'">
                            <input type="radio" name="type" value="warning" x-model="type" class="text-amber-500 focus:ring-0">
                            <span class="text-xs">⚠️ Peringatan</span>
                        </label>
                        <label class="flex items-center space-x-2.5 p-3 rounded-xl border cursor-pointer transition"
                               :class="type === 'urgent' ? 'border-rose-500 bg-rose-50/50 dark:bg-rose-900/20 font-bold text-rose-700 dark:text-rose-400' : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400'">
                            <input type="radio" name="type" value="urgent" x-model="type" class="text-rose-600 focus:ring-0">
                            <span class="text-xs">🚨 Mendesak</span>
                        </label>
                    </div>
                </div>

                <!-- Target Penerima Notifikasi -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Pilih Target Penerima <span class="text-rose-500">*</span>
                    </label>

                    <!-- Radio Tabs -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-4">
                        <button type="button" @click="targetType = 'all'"
                                :class="targetType === 'all' ? 'bg-primary text-white shadow-xs font-bold' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-semibold'"
                                class="py-2.5 px-3 rounded-xl text-xs transition text-center cursor-pointer">
                            Semua Pengguna
                        </button>
                        <button type="button" @click="targetType = 'role'"
                                :class="targetType === 'role' ? 'bg-primary text-white shadow-xs font-bold' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-semibold'"
                                class="py-2.5 px-3 rounded-xl text-xs transition text-center cursor-pointer">
                            Berdasarkan Peran
                        </button>
                        <button type="button" @click="targetType = 'class'"
                                :class="targetType === 'class' ? 'bg-primary text-white shadow-xs font-bold' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-semibold'"
                                class="py-2.5 px-3 rounded-xl text-xs transition text-center cursor-pointer">
                            Berdasarkan Kelas
                        </button>
                        <button type="button" @click="targetType = 'users'"
                                :class="targetType === 'users' ? 'bg-primary text-white shadow-xs font-bold' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-semibold'"
                                class="py-2.5 px-3 rounded-xl text-xs transition text-center cursor-pointer">
                            Pilih Pengguna
                        </button>
                    </div>
                    <input type="hidden" name="target_type" :value="targetType">

                    <!-- Target: Peran (Role) -->
                    <div x-show="targetType === 'role'" class="p-4 bg-slate-50 dark:bg-[#1A222C] rounded-xl border border-slate-200 dark:border-slate-800 space-y-2.5">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-2">Pilih Peran Pengguna:</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach($roles as $role)
                            <label class="flex items-center space-x-2 text-xs text-slate-700 dark:text-slate-300 cursor-pointer">
                                <input type="checkbox" name="roles[]" value="{{ $role['slug'] }}"
                                       class="rounded text-primary focus:ring-0"
                                       :checked="selectedRoles.includes('{{ $role['slug'] }}')">
                                <span>{{ $role['name'] }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Target: Kelas (Class) -->
                    <div x-show="targetType === 'class'" class="p-4 bg-slate-50 dark:bg-[#1A222C] rounded-xl border border-slate-200 dark:border-slate-800">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-2">Pilih Rombongan Belajar / Kelas:</span>
                        <select name="class_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#24303F] text-xs text-slate-800 dark:text-white outline-none">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Target: Pengguna Spesifik (Users) -->
                    <div x-show="targetType === 'users'" class="p-4 bg-slate-50 dark:bg-[#1A222C] rounded-xl border border-slate-200 dark:border-slate-800">
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-2">Pilih User Tertentu:</span>
                        <select name="user_ids[]" multiple class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#24303F] text-xs text-slate-800 dark:text-white outline-none h-36">
                            @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                        <span class="text-[10px] text-slate-400 mt-1 block">Tahan Ctrl (Windows) atau Command (Mac) untuk memilih lebih dari satu user.</span>
                    </div>
                </div>

                <!-- Tautan Aksi Opsional -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Tautan Aksi (Opsional)
                    </label>
                    <input type="text"
                           name="action_url"
                           x-model="actionUrl"
                           placeholder="Contoh: {{ route('admin.teacher-attendances.index') }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#1A222C] text-slate-800 dark:text-white text-xs font-mono focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition">
                    <span class="text-[11px] text-slate-400 mt-1 block">Halaman yang akan terbuka saat notifikasi di HP / PC diklik.</span>
                </div>

                <!-- Tombol Submit -->
                <div class="pt-2">
                    <button type="submit"
                            class="w-full py-3.5 px-5 bg-primary hover:bg-primary/90 active:scale-98 text-white font-extrabold text-xs rounded-xl shadow-lg shadow-primary/25 transition cursor-pointer flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        <span>Kirim Notifikasi Siaran Sekarang</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Live Preview Box (Tampilan HP & PC) -->
        <div class="lg:col-span-5 space-y-6">

            <!-- Mobile Notification Card Preview -->
            <div class="bg-white dark:bg-[#24303F] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs">
                <h3 class="text-xs font-extrabold text-slate-800 dark:text-white uppercase tracking-wider mb-3 flex items-center gap-1.5">
                    <span>📱</span> Pratinjau di Layar HP (Mobile Push)
                </h3>
                
                <div class="bg-slate-900 rounded-3xl p-4 shadow-xl border border-slate-800 text-white max-w-sm mx-auto">
                    <div class="w-12 h-1 bg-slate-700 rounded-full mx-auto mb-3"></div>

                    <!-- Push Banner Simulation -->
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3.5 border border-white/15 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <img src="/pwa-icon/192" alt="Logo" class="w-5 h-5 rounded-md object-contain bg-white p-0.5">
                                <span class="text-[11px] font-bold tracking-tight text-slate-200">{{ Setting::get('school_short_name', 'SISMI') }}</span>
                            </div>
                            <span class="text-[10px] text-slate-400">Sekarang</span>
                        </div>

                        <div>
                            <h4 class="text-xs font-black text-white leading-snug" x-text="title || 'Judul Pemberitahuan Akan Muncul di Sini'"></h4>
                            <p class="text-[11px] text-slate-300 mt-0.5 line-clamp-2 leading-relaxed" x-text="message || 'Isi teks notifikasi yang Anda ketikkan akan tampil seperti ini di layar ponsel santri/guru.'"></p>
                        </div>

                        <div class="pt-1 flex items-center justify-between text-[10px]">
                            <span class="px-2 py-0.5 rounded-full font-bold uppercase tracking-wider" :class="getTypeBadgeClass()" x-text="getTypeLabel()"></span>
                            <span class="text-slate-400 italic">Ketuk untuk membuka</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Desktop PC Notification Preview -->
            <div class="bg-white dark:bg-[#24303F] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-xs">
                <h3 class="text-xs font-extrabold text-slate-800 dark:text-white uppercase tracking-wider mb-3 flex items-center gap-1.5">
                    <span>💻</span> Pratinjau di Layar Komputer (Desktop Banner)
                </h3>

                <div class="bg-slate-100 dark:bg-[#1A222C] rounded-2xl p-4 border border-slate-200 dark:border-slate-700/80 shadow-md">
                    <div class="flex items-start space-x-3">
                        <img src="/pwa-icon/192" alt="Logo" class="w-9 h-9 rounded-xl object-contain bg-white dark:bg-slate-800 p-1 border border-slate-200 dark:border-slate-700 shrink-0">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-extrabold uppercase text-primary tracking-wider">{{ Setting::get('school_short_name', 'SISMI') }} NOTIFIKASI</span>
                                <span class="text-[10px] text-slate-400">Baru saja</span>
                            </div>
                            <h4 class="text-xs font-bold text-slate-900 dark:text-white mt-0.5" x-text="title || 'Judul Notifikasi PC'"></h4>
                            <p class="text-[11px] text-slate-600 dark:text-slate-300 mt-0.5 line-clamp-2" x-text="message || 'Pesan notifikasi pop-up di sudut layar Windows / Mac.'"></p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Riwayat Notifikasi Terkirim (Sent History) -->
    <div class="bg-white dark:bg-[#24303F] rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-xs">
        <h2 class="text-base font-extrabold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
            <span>📜</span> Riwayat Siaran Notifikasi Terkirim
        </h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-[#1A222C] text-[11px] font-extrabold text-slate-500 uppercase tracking-wider border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3">Waktu</th>
                        <th class="px-4 py-3">Pengirim</th>
                        <th class="px-4 py-3">Pemberitahuan</th>
                        <th class="px-4 py-3">Tipe & Sasaran</th>
                        <th class="px-4 py-3 text-center">Penerima</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($broadcasts as $item)
                    <tr class="hover:bg-slate-50/70 dark:hover:bg-[#1A222C]/50 transition">
                        <td class="px-4 py-3 whitespace-nowrap text-[11px]">
                            <span class="font-bold text-slate-800 dark:text-slate-200 block">{{ $item->created_at->format('d M Y') }}</span>
                            <span class="text-slate-400 text-[10px]">{{ $item->created_at->format('H:i') }} WITA</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="font-semibold text-slate-800 dark:text-white">{{ $item->sender->name ?? 'Sistem Otomatis' }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-bold text-slate-900 dark:text-white block">{{ $item->title }}</span>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-1 mt-0.5">{{ $item->message }}</p>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full border {{ $item->type_badge_class }}">
                                {{ $item->type_label }}
                            </span>
                            <span class="text-[11px] text-slate-400 block mt-1">
                                @if($item->target_type === 'all')
                                    Semua Pengguna
                                @elseif($item->target_type === 'role')
                                    Peran Tertentu
                                @elseif($item->target_type === 'class')
                                    Kelas Tertentu
                                @else
                                    Pengguna Spesifik
                                @endif
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 font-extrabold text-[11px]">
                                {{ $item->sent_count }} Perangkat
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center whitespace-nowrap">
                            <form action="{{ route('admin.notifications.broadcast.delete', $item->id) }}" method="POST" onsubmit="return confirm('Hapus riwayat siaran ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition" title="Hapus Riwayat">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-400">
                            Belum ada riwayat siaran notifikasi. Silakan buat siaran pertama Anda melalui formulir di atas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $broadcasts->links() }}
        </div>
    </div>

</div>
@endsection
